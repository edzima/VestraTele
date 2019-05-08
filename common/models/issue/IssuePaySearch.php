<?php

namespace common\models\issue;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssuePaySearch represents the model behind the search form of `common\models\issue\IssuePay`.
 */
class IssuePaySearch extends IssuePay {

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'issue_id'], 'integer'],
			[['date'], 'safe'],
			[['value'], 'number'],
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
		$query = IssuePay::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);
		$query->orderBy('date');
		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'issue_id' => $this->issue_id,
			'date' => $this->date,
			'value' => $this->value,
		]);

		return $dataProvider;
	}
}
