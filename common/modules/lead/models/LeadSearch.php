<?php

namespace common\modules\lead\models;

use common\models\SearchModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadSearch represents the model behind the search form of `common\modules\lead\models\Lead`.
 */
class LeadSearch extends Lead implements SearchModel {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id'], 'integer'],
			[['date_at', 'source', 'data', 'phone', 'email', 'postal_code'], 'safe'],
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
	public function search(array $params): ActiveDataProvider {
		$query = Lead::find();

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
			'date_at' => $this->date_at,
		]);

		$query->andFilterWhere(['like', 'source', $this->source])
			->andFilterWhere(['like', 'data', $this->data])
			->andFilterWhere(['like', 'phone', $this->phone])
			->andFilterWhere(['like', 'email', $this->email])
			->andFilterWhere(['like', 'postal_code', $this->postal_code]);

		return $dataProvider;
	}
}
