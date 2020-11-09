<?php

namespace frontend\models;

use common\models\issue\Issue;
use yii\data\ActiveDataProvider;

class ClientIssueSearch extends IssueSearch {

	public function rules(): array {
		return [
			[['customerLastname'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function search(array $params): ActiveDataProvider {
		$query = Issue::find();

		$query->with([
			'type',
			'stage.types',
		]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'updated_at' => SORT_DESC,
				],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			$query->where('0=1');
			$this->clearErrors();
			return $dataProvider;
		}
		$this->archiveFilter($query);
		$this->customerFilter($query);
		return $dataProvider;
	}
}
