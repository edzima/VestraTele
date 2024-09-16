<?php

namespace common\models\settlement\search;

use common\models\settlement\query\SettlementTypeQuery;
use common\models\settlement\SettlementType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SettlementTypeSearch represents the model behind the search form of `common\models\settlement\SettlementType`.
 */
class SettlementTypeSearch extends SettlementType {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'is_active', 'visibility_status'], 'integer'],
			[['name', 'issue_types', 'options'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
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
	public function search($params) {
		$query = SettlementType::find();
		$query->with('issueTypes');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$this->applyIssueTypesFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'is_active' => $this->is_active,
			'visibility_status' => $this->visibility_status,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'options', $this->options]);

		return $dataProvider;
	}

	protected function applyIssueTypesFilter(SettlementTypeQuery $query): void {
		if (!empty($this->issue_types)) {
			$query->forIssueTypes((array) $this->issue_types);
		}
	}

}
