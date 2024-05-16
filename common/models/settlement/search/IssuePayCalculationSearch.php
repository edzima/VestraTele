<?php

namespace common\models\settlement\search;

use common\models\AgentSearchInterface;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueStage;
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
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;
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

	public const SCENARIO_ARCHIVE = 'archive';
	public const SCENARIO_OWNER = 'owner';

	public $agent_id;
	public string $customerLastname = '';
	public $issue_type_id;
	public $issue_stage_id;

	/**
	 * @var int[]|null
	 */
	public array $issueUsersIds = [];
	public bool $withIssueStage = false;
	public bool $withCustomer = true;
	public bool $withAgents = true;
	public bool $withArchive = false;
	public bool $withArchiveDeep = false;

	public ?bool $withoutProvisions = null;

	public ?bool $onlyWithPayProblems = null;
	public bool $onlyToPayed = false;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['issue_id', 'stage_id', 'problem_status', 'owner_id'], 'integer'],
			[['!owner_id'], 'required', 'on' => static::SCENARIO_OWNER],
			['type', 'in', 'range' => array_keys(static::getTypesNames()), 'allowArray' => true],
			['issue_type_id', 'in', 'range' => array_keys($this->getIssueTypesNames()), 'allowArray' => true],
			['issue_stage_id', 'in', 'range' => array_keys(static::getIssueStagesNames()), 'allowArray' => true, 'when' => function (): bool { return $this->withIssueStage; }],

			['agent_id', 'in', 'range' => array_keys($this->getAgentsNames()), 'allowArray' => true],
			['problem_status', 'in', 'range' => array_keys(static::getProblemStatusesNames())],
			[['value'], 'number'],
			['withArchive', 'boolean', 'on' => static::SCENARIO_ARCHIVE],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
		];
	}

	public function attributeLabels(): array {
		return array_merge(
			parent::attributeLabels(),
			[
				'withArchive' => Yii::t('common', 'With Archive'),
			]
		);
	}

	public function getAgentsNames(): array {
		$ids = IssuePayCalculation::find()
			->select('user_id')
			->joinWith([
				'issue.agent',
			])
			->distinct()
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
				if (!$this->getWithArchive()) {
					$query->withoutArchives();
				}
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
		$this->applyCustomerNameFilter($query);
		$this->applyIssueUsersFilter($query);
		$this->applyProblemStatusFilter($query);
		$this->applyIssueStageFilter($query);
		$this->applyIssueTypeFilter($query);
		$this->applyToPayedPaysFilter($query);
		$this->applyWithoutProvisionsFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			IssuePayCalculation::tableName() . '.value' => $this->value,
			IssuePayCalculation::tableName() . '.type' => $this->type,
			IssuePayCalculation::tableName() . '.stage_id' => $this->stage_id,
			IssuePayCalculation::tableName() . '.issue_id' => $this->issue_id,

		]);

		$query->andFilterWhere([IssuePayCalculation::tableName() . '.owner_id' => $this->owner_id]);

		return $dataProvider;
	}

	protected function applyToPayedPaysFilter(IssuePayCalculationQuery $query): void {
		if ($this->onlyToPayed === true) {
			$query->joinWith([
				'pays P' => function (IssuePayQuery $payQuery) {
					$payQuery->onlyUnpaid();
				},
			]);
		}
	}

	protected function applyProblemStatusFilter(IssuePayCalculationQuery $query): void {
		if ($this->onlyWithPayProblems === false) {
			$query->onlyWithoutProblems();
		} elseif ($this->onlyWithPayProblems === true) {
			$query->onlyProblems(static::$paysProblems);
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

	public function applyCustomerNameFilter(QueryInterface $query): void {
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

	public function applyIssueStageFilter(ActiveQueryInterface $query): void {
		if ($this->withIssueStage) {
			$query->joinWith('issue.stage IS');
			$query->andFilterWhere(['IS.id' => $this->issue_stage_id]);
		}
	}

	public function applyIssueTypeFilter(QueryInterface $query): void {
		if (!empty($this->issue_type_id)) {
			$query->andWhere(['IT.id' => $this->issue_type_id]);
		}
	}

	public function getIssueTypesNames(): array {
		return IssueType::getTypesNamesWithShort();
	}

	public static function getIssueStagesNames(): array {
		return IssueStage::getStagesNames(true);
	}

	//@todo add archive filter when withArchive is true.
	public function getWithArchive(): bool {
		return $this->withArchive;
	}

	public function getWithArchiveDeep(): bool {
		return $this->withArchiveDeep;
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
