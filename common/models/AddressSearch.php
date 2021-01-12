<?php

namespace common\models;

use edzima\teryt\models\Region;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;

class AddressSearch extends Address implements SearchModel {

	public $region_id;
	public $city_name;

	public function rules(): array {
		return [
			[['region_id', 'id'], 'integer'],
			[['city_name', 'postal_code', 'info'], 'string'],
		];
	}

	public function attributeLabels(): array {
		return [
			'region_id' => Yii::t('address', 'Region'),
			'city_name' => Yii::t('address', 'City'),
			'postal_code' => Yii::t('address', 'Code'),

		];
	}

	public function search(array $params): DataProviderInterface {
		$query = Address::find();

		$query->joinWith('users.userProfile UP');
		$query->joinWith('meets M');

		$this->load($params);
		$this->applySearch($query);

		$totalQuery = clone $query;
		$totalQuery->joinWith = [];
		$totalQuery->joinWith('city C');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'totalCount' => (int) $totalQuery->count(),
		]);

		return $dataProvider;
	}

	public function applySearch(ActiveQuery $query): void {
		if ($this->validate()) {
			$query->joinWith('city C');
			$query->alias('A');
			$this->applyCityNameFilter($query);
			$this->applyPostalCodeFilter($query);
			$this->applyRegionFilter($query);
		}
	}

	private function applyPostalCodeFilter(QueryInterface $query): void {
		if (!empty($this->postal_code)) {
			$query->andWhere(['like', 'A.postal_code', $this->postal_code . '%', false]);
		}
	}

	private function applyCityNameFilter(QueryInterface $query): void {
		if (!empty($this->city_name)) {
			$query->andWhere(['like', 'C.name', $this->city_name . '%', false]);
		}
	}

	private function applyRegionFilter(QueryInterface $query): void {
		$query->andFilterWhere(['C.region_id' => $this->region_id]);
	}

	public function getRegionsNames(): array {
		return Region::getNames();
	}
}
