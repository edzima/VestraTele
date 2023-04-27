<?php

namespace common\models;

use edzima\teryt\models\Region;
use edzima\teryt\models\Terc;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;

/**
 * Class AddressSearch
 *
 * @todo move to edzima\teryt
 */
class AddressSearch extends Address implements SearchModel {

	public $region_id;
	public $district_id;
	public $commune_id;
	public $city_name;

	public function isNotEmpty(): bool {
		return !empty($this->region_id)
			|| !empty($this->district_id)
			|| !empty($this->commune_id)
			|| !empty($this->postal_code)
			|| !empty($this->city_name)
			|| !empty($this->info);
	}

	public function rules(): array {
		return [
			[['region_id', 'id', 'district_id', 'commune_id'], 'integer'],
			[['city_name', 'postal_code', 'info'], 'string'],
		];
	}

	public function attributeLabels(): array {
		return [
			'region_id' => Yii::t('address', 'Region'),
			'district_id' => Yii::t('address', 'District'),
			'commune_id' => Yii::t('address', 'Commune'),
			'city_name' => Yii::t('address', 'City'),
			'postal_code' => Yii::t('address', 'Code'),
		];
	}

	public function search(array $params): DataProviderInterface {
		$query = Address::find();

		$query->joinWith('users.userProfile UP');

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

	public function getRegionsData(): array {
		return Region::getNames();
	}

	public function getDistrictsData(): array {
		if (empty($this->region_id)) {
			return [];
		}
		return ArrayHelper::map(
			Terc::find()
				->onlyDistricts($this->region_id)
				->asArray()
				->all(),
			'district_id',
			'name'
		);
	}

	public function getCommunesData(): array {
		if (empty($this->region_id) || empty($this->district_id)) {
			return [];
		}
		return ArrayHelper::map(
			Terc::find()
				->onlyCommunes($this->region_id, $this->district_id)
				->asArray()
				->all(),
			'commune_id',
			'name'
		);
	}

	public function applySearch(ActiveQuery $query): void {
		if ($this->validate() && $this->isNotEmpty()) {
			$query->joinWith('city C');
			$query->alias('A');
			$this->applyCityNameFilter($query);
			$this->applyPostalCodeFilter($query);
			$this->applyInfoFilter($query);
			$this->applyRegionFilter($query);
			$this->applyDistrictFilter($query);
			$this->applyCommuneFilter($query);
		}
	}

	private function applyPostalCodeFilter(QueryInterface $query): void {
		if (!empty($this->postal_code)) {
			$query->andWhere(['like', 'A.postal_code', $this->postal_code . '%', false]);
		}
	}

	private function applyInfoFilter(QueryInterface $query): void {
		if (!empty($this->info)) {
			$query->andWhere(['like', 'A.info', $this->info]);
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

	private function applyDistrictFilter(ActiveQuery $query): void {
		$query->andFilterWhere(['C.district_id' => $this->district_id]);
	}

	private function applyCommuneFilter(ActiveQuery $query): void {
		$query->andFilterWhere(['C.commune_id' => $this->commune_id]);
	}
}
