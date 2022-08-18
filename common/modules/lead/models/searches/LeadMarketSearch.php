<?php

namespace common\modules\lead\models\searches;

use common\models\AddressSearch;
use common\models\user\User;
use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * LeadMarketSearch represents the model behind the search form of `common\modules\lead\models\LeadMarket`.
 */
class LeadMarketSearch extends LeadMarket {

	private const WITHOUT_MARKET_USERS = -10;

	public $userStatus;

	public $visibleArea;

	public $leadStatus;
	public $leadName;
	public $leadType;

	public $userId;

	public $selfAssign;
	public $selfMarket;

	public $withoutArchive;
	public $withoutCity;

	public ?AddressSearch $addressSearch = null;

	public static function getMarketUserStatusesNames(): array {
		$statuses = LeadMarketUser::getStatusesNames();
		$statuses[self::WITHOUT_MARKET_USERS] = Yii::t('lead', 'Without Market Users');
		return $statuses;
	}

	public function attributeLabels(): array {
		return parent::attributeLabels() + [
				'selfAssign' => Yii::t('lead', 'Self Assign'),
				'selfMarket' => Yii::t('lead', 'Self Market'),
				'withoutCity' => Yii::t('lead', 'Without City'),
				'withoutArchive' => Yii::t('lead', 'Without Archives'),
				'leadStatus' => Yii::t('lead', 'Lead Status'),
			];
	}

	public function __construct($config = []) {
		if (!isset($config['addressSearch'])) {
			$config['addressSearch'] = new AddressSearch();
		}
		parent::__construct($config);
	}

	public static function getVisibleAreaNames(): array {
		return LeadMarketOptions::visibleAreaNames();
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[
				[
					'!userId', 'creator_id', 'id', 'lead_id', 'status', 'creator_id',
					'visibleArea', 'leadStatus', 'userStatus',
					'leadStatus', 'leadType',
				], 'integer',
			],
			[['selfAssign', 'selfMarket', 'withoutArchive', 'withoutCity'], 'boolean'],
			[['created_at', 'updated_at', 'options', 'booleanOptions', 'details', 'leadName'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios(): array {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search(array $params = []) {
		$query = LeadMarket::find();

		$query->with([
			'creator.userProfile',
			'lead',
			'lead.owner',
			'lead.leadSource',
			'leadMarketUsers',
		]);
		$query->groupBy(LeadMarket::tableName() . '.id');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'created_at' => SORT_DESC,
				],
			],
		]);

		$this->load($params);
		if ($this->withoutCity) {
			$this->addressSearch = null;
		}
		if ($this->addressSearch) {
			$this->addressSearch->load($params);
		}

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			$query->where('0=1');
			return $dataProvider;
		}

		$this->applyAddressFilter($query);
		$this->applyVisibleAreaFilter($query);
		$this->applySelfMarketFilter($query);
		$this->applySelfAssignFilter($query);
		$this->applyWithoutArchiveFilter($query);
		$this->applyLeadNameFilter($query);
		$this->applyLeadStatusFilter($query);
		$this->applyLeadTypeFilter($query);
		$this->applyMarketUserStatusFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			LeadMarket::tableName() . '.id' => $this->id,
			LeadMarket::tableName() . '.lead_id' => $this->lead_id,
			LeadMarket::tableName() . '.creator_id' => $this->creator_id,
			LeadMarket::tableName() . '.status' => $this->status,
			LeadMarket::tableName() . '.created_at' => $this->created_at,
			LeadMarket::tableName() . '.updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'details', $this->details]);

		return $dataProvider;
	}

	/**
	 * @param LeadMarket $models
	 * @return LeadMarket[]
	 */
	public function filterAddressOptions(array $models, AddressSearch $addressSearch = null): array {
		if ($addressSearch === null) {
			$addressSearch = $this->addressSearch;
		}
		if ($addressSearch === null || !$addressSearch->isNotEmpty()) {
			return $models;
		}
		return array_filter($models, function (LeadMarket $market) use ($addressSearch): bool {
			$options = $market->getMarketOptions();
			switch ($options->visibleArea) {
				case LeadMarketOptions::VISIBLE_ADDRESS_CITY:
					return true;
				case LeadMarketOptions::VISIBLE_ADDRESS_REGION_AND_DISTRICT:
					return !(!empty($addressSearch->city_id) || !empty($addressSearch->commune_id));
				case LeadMarketOptions::VISIBLE_ADDRESS_REGION_AND_DISTRICT_WITH_COMMUNE:
					return empty($addressSearch->city_id);
				case LeadMarketOptions::VISIBLE_ADDRESS_REGION:
					return !empty($addressSearch->region_id);
			}
			return false;
		});
	}

	private function applyAddressFilter(ActiveQuery $query): void {
		if ($this->withoutCity) {
			$query->joinWith('lead.addresses.address', false, 'LEFT OUTER JOIN');
			$query->andWhere('city_id IS NULL');
		} else {
			if ($this->addressSearch->validate()) {
				if ($this->addressSearch->isNotEmpty()) {
					$query->joinWith([
						'lead.addresses.address' => function (ActiveQuery $addressQuery) {
							$this->addressSearch->applySearch($addressQuery);
						},
					]);
				} else {
					$query->with('lead.addresses.address.city.terc');
				}
			}
		}
	}

	private function applyVisibleAreaFilter(ActiveQuery $query): void {
		if (!empty($this->visibleArea)) {
			$query->andWhere(new Expression("JSON_CONTAINS(options,:type, '$.visibleArea')", [
				'type' => (int) $this->visibleArea,
			]));
		}
	}

	private function applySelfAssignFilter(ActiveQuery $query): void {
		if (!empty($this->userId)) {
			if ($this->selfAssign === null || $this->selfAssign === '') {
				return;
			}
			if ($this->selfAssign) {
				$query->andWhere([
					LeadMarket::tableName() . '.id' => LeadMarketUser::find()
						->select('market_id')
						->andWhere(['user_id' => $this->userId]),
				]);
			} else {
				$query->andWhere([
					'not', [
						LeadMarket::tableName() . '.id' => LeadMarketUser::find()
							->select('market_id')
							->andWhere(['user_id' => $this->userId]),
					],
				]);
			}
		}
	}

	private function applySelfMarketFilter(ActiveQuery $query) {
		if (!empty($this->userId)) {

			if ($this->selfMarket === null || $this->selfMarket === '') {
				return;
			}
			if ($this->selfMarket) {
				$query->andWhere([
					LeadMarket::tableName() . '.creator_id' => $this->userId,
				],
				);
			} else {
				$query->andWhere([
					'not', [
						LeadMarket::tableName() . '.creator_id' => $this->userId,
					],
				]);
			}
		}
	}

	private function applyWithoutArchiveFilter(ActiveQuery $query): void {
		if ($this->withoutArchive && $this->status !== LeadMarket::STATUS_ARCHIVED) {
			$query->andWhere(['!=', LeadMarket::tableName() . '.status', LeadMarket::STATUS_ARCHIVED]);
		}
	}

	public static function getCreatorsNames(): array {
		return User::getSelectList(
			LeadMarket::find()
				->select('creator_id')
				->distinct()
				->column()
			, false);
	}

	private function applyLeadStatusFilter(ActiveQuery $query): void {
		if (!empty($this->leadStatus)) {
			$query->joinWith('lead');
			$query->andWhere([
				Lead::tableName() . '.status_id' => $this->leadStatus,
			]);
		}
	}

	private function applyLeadTypeFilter(ActiveQuery $query): void {
		if (!empty($this->leadType)) {
			$query->joinWith('lead.leadSource');
			$query->andWhere([
				LeadSource::tableName() . '.type_id' => $this->leadType,
			]);
		}
	}

	private function applyLeadNameFilter(ActiveQuery $query): void {
		if (!empty($this->leadName)) {
			$query->joinWith('lead');
			$query->andWhere([
				Lead::tableName() . '.name' => $this->leadName,
			]);
		}
	}

	private function applyMarketUserStatusFilter(ActiveQuery $query): void {
		if (!empty($this->userStatus)) {
			if ((int) $this->userStatus !== static::WITHOUT_MARKET_USERS) {
				$query->joinWith('leadMarketUsers');
				$query->andWhere([LeadMarketUser::tableName() . '.status' => $this->userStatus]);
			} else {
				$query->joinWith('leadMarketUsers', false, 'LEFT OUTER JOIN');
				$query->andWhere([
					LeadMarketUser::tableName() . '.market_id' => null,
				]);
			}
		}
	}

	public static function getLeadTypesNames(): array {
		$ids = LeadMarket::find()
			->select('type_id')
			->joinWith('lead.leadSource')
			->distinct()
			->column();

		$names = [];
		foreach ($ids as $id) {
			$names[$id] = LeadType::getNames()[$id];
		}
		return $names;
	}

	public static function getLeadStatusesNames(): array {
		$ids = LeadMarket::find()
			->select('status_id')
			->joinWith('lead')
			->distinct()
			->column();

		$names = [];
		foreach ($ids as $id) {
			$names[$id] = LeadStatus::getNames()[$id];
		}
		return $names;
	}

}
