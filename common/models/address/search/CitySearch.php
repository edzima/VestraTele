<?php

namespace common\models\address\search;

use common\models\address\City;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CitySearch represents the model behind the search form of City model.
 */
class CitySearch extends City {

	/**
	 * @inheritdoc
	 */
	public $powiat;
	public $wojewodztwo;

	public function rules() {
		return [
			[['id', 'wojewodztwo_id', 'powiat_id'], 'integer'],
			[['name', 'powiat', 'wojewodztwo'], 'safe'],
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
		$query = City::find()->joinWith(['powiatRel', 'wojewodztwo']);

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
			'miasta.id' => $this->id,
			'wojewodztwo_id' => $this->wojewodztwo_id,
			'powiat_id' => $this->powiat_id,
		]);

		$query->andFilterWhere(['like', 'miasta.name', $this->name])
			->andFilterWhere(['like', 'wojewodztwa.name', $this->wojewodztwo])
			->andFilterWhere(['like', 'powiaty.name', $this->powiat]);

		return $dataProvider;
	}
}
