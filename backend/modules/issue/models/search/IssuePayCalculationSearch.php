<?php

namespace backend\modules\issue\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\issue\IssuePayCalculation;

/**
 * IssuePayCalculationSearch represents the model behind the search form of `common\models\issue\IssuePayCalculation`.
 */
class IssuePayCalculationSearch extends IssuePayCalculation {

	public $client_surname;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['issue_id', 'type'], 'integer'],
			[['value'], 'number'],
			[['client_surname'], 'safe'],
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
	public function search($params): ActiveDataProvider {
		$query = IssuePayCalculation::find();
		$query->joinWith('issue');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => 'updated_at DESC',
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'value' => $this->value,
			'type' => $this->type,
		]);

		$query
			->andFilterWhere(['like', 'issue.client_surname', $this->client_surname])
			->andFilterWhere(['like', 'issue.id', $this->issue_id]);

		return $dataProvider;
	}

}
