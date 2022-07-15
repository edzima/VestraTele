<?php

namespace common\modules\lead\models\searches;

use common\models\AddressSearch;
use common\models\user\User;
use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\LeadStatus;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * LeadMarketSearch represents the model behind the search form of `common\modules\lead\models\LeadMarket`.
 */
class LeadMarketSearch extends LeadMarket {

	public $visibleArea;

	public $leadStatus;

	public $userId;

	public $selfAssign;
	public $selfMarket;

	public $withoutArchive;

	public ?AddressSearch $addressSearch = null;

	public static function getLeadStatusesNames(): array {
		return LeadStatus::getNames();
	}

	public function attributeLabels(): array {
		return parent::attributeLabels() + [
				'selfAssign' => Yii::t('lead', 'Self Assign'),
				'selfMarket' => Yii::t('lead', 'Self Market'),
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
			[['!userId', 'creator_id', 'id', 'lead_id', 'status', 'creator_id', 'visibleArea', 'leadStatus'], 'integer'],
			[['selfAssign', 'selfMarket'], 'boolean'],
			[['created_at', 'updated_at', 'options', 'booleanOptions', 'details'], 'safe'],
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
		$query->joinWith('leadMarketUsers');
		$query->with([
			'creator.userProfile',
			'lead',
			'lead.owner',
		]);
		$query->groupBy(LeadMarket::tableName() . '.id');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);
		$this->addressSearch->load($params);

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

		// grid filtering conditions
		$query->andFilterWhere([
			LeadMarket::tableName() . '.id' => $this->id,
			LeadMarket::tableName() . '.lead_id' => $this->lead_id,
			LeadMarket::tableName() . '.creator_id' => $this->creator_id,
			LeadMarket::tableName() . '.status' => $this->status,
			LeadMarket::tableName() . '.created_at' => $this->created_at,
			LeadMarket::tableName() . '.updated_at' => $this->updated_at,
			Lead::tableName() . '.status' => $this->leadStatus,
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
		if ($this->addressSearch->validate()) {
			$query->joinWith([
				'lead.addresses.address' => function (ActiveQuery $addressQuery) {
					$this->addressSearch->applySearch($addressQuery);
				},
			]);
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
}
