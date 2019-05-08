<?php

namespace common\models\issue;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssueTypeSearch represents the model behind the search form of `common\models\issue\IssueType`.
 */
class IssueTypeSearch extends IssueType {

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['provision_type'], 'integer'],
			[['name', 'short_name'], 'safe'],
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
		$query = IssueType::find();

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
			'provision_type' => $this->provision_type,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'short_name', $this->short_name]);

		return $dataProvider;
	}
}
