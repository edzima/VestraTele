<?php

namespace common\models\hint\searches;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\hint\HintSource;

/**
 * HintSourceSearch represents the model behind the search form of `common\models\hint\HintSource`.
 */
class HintSourceSearch extends HintSource {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'is_active'], 'integer'],
			[['name', 'short_name'], 'safe'],
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
		$query = HintSource::find();

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
		]);

		$query
			->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'short_name', $this->short_name]);

		return $dataProvider;
	}
}
