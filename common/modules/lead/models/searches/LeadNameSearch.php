<?php

namespace common\modules\lead\models\searches;

use common\models\SearchModel;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\query\LeadQuery;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class LeadNameSearch extends Lead implements SearchModel {

	public $type_id;

	public static function getTypesNames(): array {
		return LeadType::getNames();
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	public function rules(): array {
		return [
			['name', 'required'],
			[['type_id', 'status_id'], 'integer'],
			['name', 'trim'],
			['name', 'string', 'min' => 3],
		];
	}

	public function search(array $params): ActiveDataProvider {
		$query = Lead::find();

		$query->with(['owner', 'owner.userProfile']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'defaultPageSize' => 50,
			],
			'sort' => [
				'defaultOrder' => [
					'date_at' => SORT_DESC,
					'name' => SORT_ASC,
				],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->andWhere('0=1');
			return $dataProvider;
		}
		$this->applyNameFilter($query);
		$this->applyStatusFilter($query);
		$this->applyTypeFilter($query);
		return $dataProvider;
	}

	private function applyTypeFilter(LeadQuery $query) {
		if (!empty($this->type_id)) {
			$query->type($this->type_id);
		}
	}

	private function applyNameFilter(LeadQuery $query): void {
		$query->andWhere(
			new Expression(
				"SUBSTRING_INDEX(" . Lead::tableName() . ".name,' ',1) = :name OR "
				. "SUBSTRING_INDEX(" . Lead::tableName() . ".name,' ',-1) = :name",
				['name' => $this->name]));
	}

	private function applyStatusFilter(LeadQuery $query): void {
		$query->andFilterWhere([Lead::tableName() . '.status_id' => $this->status_id]);
	}
}
