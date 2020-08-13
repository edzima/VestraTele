<?php

namespace backend\modules\issue\models\searches;

use common\models\issue\Issue;
use yii\data\ActiveDataProvider;

class NewPayCalculationSearch extends Issue {

	public function rules(): array {
		return [
			[['id', 'client_surname'], 'string'],
		];
	}

	public function search(array $params = []): ActiveDataProvider {
		$query = Issue::find();
		$query->joinWith('payCalculations as calculations');
		$query->onlyPositiveDecision();
		$query->andWhere('calculations.issue_id IS NULL');

		$this->load($params);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => 'updated_at DESC',
			],
		]);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$query
			->andFilterWhere(['like', 'issue.client_surname', $this->client_surname])
			->andFilterWhere(['like', 'issue.id', $this->id]);

		return $dataProvider;
	}

}
