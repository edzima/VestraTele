<?php

namespace common\modules\court\models\search;

use common\modules\court\models\Court;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CourtSearch represents the model behind the search form of `common\modules\court\models\Court`.
 */
class CourtSearch extends Court {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'parent_id'], 'integer'],
			[['name', 'address', 'type', 'phone', 'fax', 'email', 'updated_at'], 'safe'],
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
		$query = Court::find();

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
			'updated_at' => $this->updated_at,
			'parent_id' => $this->parent_id,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'address', $this->address])
			->andFilterWhere(['like', 'type', $this->type])
			->andFilterWhere(['like', 'phone', $this->phone])
			->andFilterWhere(['like', 'fax', $this->fax])
			->andFilterWhere(['like', 'email', $this->email]);

		return $dataProvider;
	}
}
