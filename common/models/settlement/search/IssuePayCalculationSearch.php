<?php

namespace common\models\settlement\search;

use common\models\AgentSearchInterface;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueType;
use common\models\issue\query\IssuePayCalculationQuery;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\issue\query\IssueUserQuery;
use common\models\issue\search\ArchivedIssueSearch;
use common\models\issue\search\IssueTypeSearch;
use common\models\SearchModel;
use common\models\user\CustomerSearchInterface;
use common\models\user\query\UserQuery;
use common\models\user\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * IssuePayCalculationSearch represents the model behind the search form of `common\models\issue\IssuePayCalculation`.
 */
class IssuePayCalculationSearch extends IssuePayCalculation implements
	CustomerSearchInterface,
	IssueTypeSearch,
	ArchivedIssueSearch,
	AgentSearchInterface,
	SearchModel {

	public $agent_id;
	public string $customerLastname = '';
	public $issue_type_id;

	/**
	 * @var int[]|null
	 */
	public array $issueUsersIds = [];
	public bool $withCustomer = true;
	public bool $withAgents = true;
	public ?bool $withoutProvisions = null;

	public ?bool $onlyWithProblems = null;
	public bool $onlyToPayed = false;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['issue_id', 'stage_id', 'type', 'problem_status', 'owner_id'], 'integer'],
			['issue_type_id', 'in', 'range' => array_keys(static::getIssueTypesNames()), 'allowArray' => true],
			['agent_id', 'in', 'range' => array_keys($this->getAgentsNames()), 'allowArray' => true],
			['problem_status', 'in', 'range' => array_keys(static::getProblemStatusesNames())],
			[['value'], 'number'],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
		];
	}

	public function getAgentsNames(): array {
		$ids = IssuePayCalculation::find()
			->select('user_id')
			->joinWith([
				'issue.agent',
			])
			->column();
		return User::getSelectList($ids);
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

		$query->joinWith([
			'issue' => function (IssueQuery $query): void {
				$query->withoutArchives();
			},
		]);
		$query->joinWith('issue.type IT');
		$query->joinWith('owner O');
		$query->joinWith('pays P');

		$query->distinct();

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
			$query->andWhere('0=1');
			return $dataProvider;
		}
		$this->applyAgentsFilters($query);
		$this->applyCustomerSurnameFilter($query);
		$this->applyIssueUsersFilter($query);
		$this->applyProblemStatusFilter($query);
		$this->applyIssueTypeFilter($query);
		$this->applyToPayedPaysFilter($query);
		$this->applyWithoutProvisionsFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			IssuePayCalculation::tableName() . '.value' => $this->value,
			IssuePayCalculation::tableName() . '.type' => $this->type,
			IssuePayCalculation::tableName() . '.stage_id' => $this->stage_id,
		]);

		$query->andFilterWhere(['like', 'issue.id', $this->issue_id]);
		$query->andFilterWhere(['like', 'owner_id', $this->owner_id]);

		return $dataProvider;
	}

	protected function applyToPayedPaysFilter(IssuePayCalculationQuery $query): void {
		if ($this->onlyToPayed === true) {
			$query->joinWith([
				'pays P' => function (IssuePayQuery $payQuery) {
					$payQuery->onlyNotPayed();
				},
			]);
		}
	}

	protected function applyProblemStatusFilter(IssuePayCalculationQuery $query): void {
		if ($this->onlyWithProblems === false) {
			$query->onlyWithoutProblems();
		} elseif ($this->onlyWithProblems === true) {
			$query->onlyProblems();
		}
		if ($this->problem_status > 0) {
			$query->onlyProblems((array) $this->problem_status);
		}
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
			]);
		}
	}

	public function applyAgentsFilters(QueryInterface $query): void {
		/** @var IssuePayCalculationQuery $query */
		if ($this->withAgents || !empty($this->agent_id)) {
			$query->joinWith([
				'issue.agent' => function (UserQuery $query) {
					$query->joinWith('userProfile AP');
				},
			]);
		}
		if (!empty($this->agent_id)) {
			$query->joinWith([
				'issue' => function (IssueQuery $query): void {
					$query->agents((array) $this->agent_id);
				},
			]);
		}
	}

	public function applyCustomerSurnameFilter(QueryInterface $query): void {
		if ($this->withCustomer || !empty($this->customerLastname)) {
			$query->joinWith([
				'issue.customer C' => function (UserQuery $query) {
					$query->joinWith('userProfile CP');
				},
			]);
		}
		if (!empty($this->customerLastname)) {
			$query->andWhere(['like', 'CP.lastname', $this->customerLastname . '%', false]);
		}
	}

	public function applyIssueTypeFilter(QueryInterface $query): void {
		if (!empty($this->issue_type_id)) {
			$query->andWhere(['IT.id' => $this->issue_type_id]);
		}
	}

	public static function getIssueTypesNames(): array {
		return IssueType::getTypesNames();
	}

	//@todo add archive filter when withArchive is true.
	public function getWithArchive(): bool {
		return $this->withAchive;
	}

	private function applyIssueUsersFilter(IssuePayCalculationQuery $query): void {
		if (!empty($this->issueUsersIds)) {
			$query->joinWith([
				'issue.users IU' => function (IssueUserQuery $query): void {
					$query->andWhere(['IU.user_id' => $this->issueUsersIds]);
				},
			]);
		}
	}

	public static function getProblemStatusesNames(): array {
		return IssuePayCalculation::getProblemStatusesNames();
	}

}