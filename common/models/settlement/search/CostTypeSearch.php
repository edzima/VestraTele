<?php

namespace common\models\settlement\search;

use common\models\settlement\CostType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CostTypeSearch represents the model behind the search form of `common\models\settlement\CostType`.
 */
class CostTypeSearch extends CostType {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'is_active', 'is_for_settlement'], 'integer'],
			[['name', 'options'], 'safe'],
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
		$query = CostType::find();

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

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'is_active' => $this->is_active,
			'is_for_settlement' => $this->is_for_settlement,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'options', $this->options]);

		return $dataProvider;
	}
}
