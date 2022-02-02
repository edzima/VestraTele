<?php

namespace backend\modules\issue\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\issue\SummonType;

/**
 * SummonTypeSearch represents the model behind the search form of `common\models\issue\SummonType`.
 */
class SummonTypeSearch extends SummonType {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'term'], 'integer'],
			[['name', 'short_name', 'title'], 'safe'],
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
	public function search($params) {
		$query = SummonType::find();

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
			'term' => $this->term,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'short_name', $this->short_name])
			->andFilterWhere(['like', 'title', $this->title]);

		return $dataProvider;
	}
}
