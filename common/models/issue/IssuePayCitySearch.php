<?php

namespace common\models\issue;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssuePayCitySearch represents the model behind the search form of `common\models\issue\IssuePayCity`.
 */
class IssuePayCitySearch extends IssuePayCity {

	public $city;

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			['city', 'string'],
			[['city_id'], 'integer'],
			[['phone', 'bank_transfer_at', 'direct_at'], 'safe'],
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
		$query = IssuePayCity::find();
		$query->joinWith('city');
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
			'bank_transfer_at' => $this->bank_transfer_at,
			'direct_at' => $this->direct_at,
		]);

		$query->andFilterWhere(['like', 'phone', $this->phone])
			->andFilterWhere(['like', 'miasta.name', $this->city]);

		return $dataProvider;
	}
}
