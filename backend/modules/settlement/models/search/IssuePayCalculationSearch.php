<?php

namespace backend\modules\settlement\models\search;

use common\models\issue\IssuePayCalculation;
use common\models\issue\query\IssuePayCalculationQuery;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\user\CustomerSearchInterface;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * IssuePayCalculationSearch represents the model behind the search form of `common\models\issue\IssuePayCalculation`.
 */
class IssuePayCalculationSearch extends IssuePayCalculation implements
	CustomerSearchInterface {

	public string $customerLastname = '';
	public ?bool $withoutProvisions = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['issue_id', 'type', 'problem_status'], 'integer'],
			[['value'], 'number'],
			[['customerLastname'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios(): array {
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
	public function search(array $params): ActiveDataProvider {
		$query = IssuePayCalculation::find();
		$query->joinWith('issue');
		$query->joinWith('issue.customer.userProfile CP');

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
		$this->applyCustomerSurnameFilter($query);
		$this->applyWithoutProvisionsFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			IssuePayCalculation::tableName() . '.value' => $this->value,
			IssuePayCalculation::tableName() . '.type' => $this->type,
			IssuePayCalculation::tableName() . 'problem_status' => $this->problem_status,
		]);

		$query
			->andFilterWhere(['like', 'issue.id', $this->issue_id]);

		return $dataProvider;
	}

	protected function applyWithoutProvisionsFilter(IssuePayCalculationQuery $query): void {
		if ($this->withoutProvisions) {
			$query->distinct();
			$query->joinWith([
				'pays P' => function (IssuePayQuery $query): void {
					$query->joinWith('provisions PR');
					$query->andWhere('P.id IS NOT NULL');
					$query->andWhere('PR.id IS NULL');
				},
				'issue' => function (IssueQuery $query): void {
					$query->withoutArchives();
				},
			]);
		}
	}

	public function applyCustomerSurnameFilter(QueryInterface $query): void {
		if (!empty($this->customerLastname)) {
			$query->andWhere(['like', 'CP.lastname', $this->customerLastname . '%', false]);
		}
	}

}
