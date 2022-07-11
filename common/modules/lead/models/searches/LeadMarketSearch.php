<?php

namespace common\modules\lead\models\searches;

use common\models\AddressSearch;
use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\LeadMarket;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * LeadMarketSearch represents the model behind the search form of `common\modules\lead\models\LeadMarket`.
 */
class LeadMarketSearch extends LeadMarket {

	public $visibleArea;
	public ?AddressSearch $addressSearch = null;

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
			[['id', 'lead_id', 'status', 'creator_id', 'usersCount', 'visibleArea'], 'integer'],
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

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);
		$this->addressSearch->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$this->applyAddressFilter($query);
		$this->applyVisibleAreaFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'lead_id' => $this->lead_id,
			'creator_id' => $this->creator_id,
			'status' => $this->status,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
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
		if ($this->addressSearch->validate() && $this->addressSearch->isNotEmpty()) {
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
}
