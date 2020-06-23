<?php

namespace common\models\address\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\address\Province;

/**
 * ProvinceSearch represents the model behind the search form of Province model.
 */
class ProvinceSearch extends Province {

	/**
	 * @inheritdoc
	 */
	public $wojewodztwo;

	public function rules() {
		return [
			[['id', 'wojewodztwo_id'], 'integer'],
			[['name', 'wojewodztwo'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
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
		$query = Province::find()->joinWith(['wojewodztwo']);

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
			'wojewodztwo_id' => $this->wojewodztwo_id,
		]);

		$query->andFilterWhere(['like', 'powiaty.name', $this->name])
			->andFilterWhere(['like', 'wojewodztwa.name', $this->wojewodztwo]);

		return $dataProvider;
	}
}
