<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueRelation;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * RelationSearch represents the model behind the search form of `common\models\issue\IssueRelation`.
 */
class RelationSearch extends IssueRelation {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id_1', 'issue_id_2', 'created_at'], 'integer'],
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
		$query = IssueRelation::find();

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
			'issue_id_1' => $this->issue_id_1,
			'issue_id_2' => $this->issue_id_2,
			'created_at' => $this->created_at,
		]);

		return $dataProvider;
	}
}
