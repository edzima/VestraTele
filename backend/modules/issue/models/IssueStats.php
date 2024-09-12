<?php

namespace backend\modules\issue\models;

use common\behaviors\IssueTypeParentIdAction;
use common\models\issue\Issue;
use common\models\issue\IssueCost;
use common\models\issue\IssuePay;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\issue\query\IssueUserQuery;
use common\models\issue\search\IssueMainTypeSearchable;
use common\models\provision\Provision;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\Connection;
use yii\db\Query;

class IssueStats extends Model implements IssueMainTypeSearchable {

	private static array $MODELS = [];
	public ?int $issueMainTypeId = null;

	public ?int $year = null;
	public ?int $month = null;
	private ?int $countYears = null;
	private ?int $countMonth = null;
	private ?int $withoutArchivesCount = null;

	private ?int $allCount = null;
	private ?int $withoutPaysCount = null;

	public ?Connection $db = null;

	public ?string $startAt = null;
	public ?string $endAt = null;

	public function rules(): array {
		return [
			[['month', 'year'], 'integer', 'min' => 1],
			[['year'], 'default', 'value' => date('Y')],
			[['month'], 'default', 'value' => date('m')],
			['issueMainTypeId', 'in', 'range' => IssueType::getTypesIds()],
		];
	}

	public function init() {
		parent::init();
		$this->initDefault();
	}

	protected function initDefault(): void {
		if ($this->month === null) {
			$this->month = date('m');
		}
		if ($this->year === null) {
			$this->year = date('Y');
		}
		if (empty($this->startAt)) {
			$this->startAt = date("Y-{$this->month}-01");
		}
	}

	public function setMonth(int $month): void {
		$this->month = $month;
		$this->startAt = date("{$this->year}-{$this->month}-01");
		$this->endAt = date("{$this->year}-{$this->month}-t");
	}

	protected function getBaseQuery(): IssueQuery {
		$query = Issue::find();
		$this->applyIssueMainTypeFilter($query);
		return $query;
	}

	public function getAllCount(): int {
		if ($this->allCount === null) {
			$query = $this->getBaseQuery();
			$this->allCount = $this->count($query);
		}
		return $this->allCount;
	}

	protected function count(ActiveQueryInterface $query, string $q = '*'): int {
		return (int) $query->count($q, $this->db);
	}

	public function getCountForYear(): int {
		if ($this->countYears === null) {
			$query = $this->getBaseQuery();
			$this->applyYearFilter($query);
			$this->countYears = $this->count($query);
		}

		return $this->countYears;
	}

	public function getCountForMonth(): int {
		if ($this->countMonth === null) {
			$query = $this->getBaseQuery();
			$this->applyMonthFilter($query);
			$this->countMonth = $this->count($query);
		}

		return $this->countMonth;
	}

	public function getArchivesCountForMonth(): int {
		return $this->getCountForMonth() - $this->getWithoutArchivesCountForMonth();
	}

	public function getWithoutArchivesCountForMonth(): int {
		if ($this->withoutArchivesCount === null) {
			$query = $this->getBaseQuery();
			$this->applyMonthFilter($query);
			$query->withoutArchives();
			$this->withoutArchivesCount = $this->count($query);
		}
		return $this->withoutArchivesCount;
	}

	public function getWithPaysCountForMonth(): int {
		return $this->getCountForMonth() - $this->getWithoutPaysCountForMonth();
	}

	public function getWithoutPaysCountForMonth(): int {
		if ($this->withoutPaysCount === null) {
			$query = $this->getBaseQuery();
			$this->applyMonthFilter($query);
			$query->onlyWithoutPay();
			$this->withoutPaysCount = $this->count($query);
		}
		return $this->withoutPaysCount;
	}

	public function getCountForToday(): int {
		$query = $this->getBaseQuery();
		$this->applyCreatedAtFilter(
			$query,
			date("Y-m-d 00:00:00"),
			date("Y-m-d 23:59:59"),
		);
		return $query->count();
	}

	public function getIssueMainType(): ?IssueType {
		return IssueType::getTypes()[$this->issueMainTypeId] ?? null;
	}

	/**
	 * @param IssueQuery $query
	 * @return void
	 */
	public function applyIssueMainTypeFilter(ActiveQuery $query): void {
		if (!empty($this->issueMainTypeId) && $this->issueMainTypeId !== IssueTypeParentIdAction::ISSUE_PARENT_TYPE_ALL) {
			$query->type($this->issueMainTypeId);
		}
	}

	public function getPreviousMonthModel(): self {
		$model = clone $this;
		$model->month = $this->getPreviousMonth();
		return $model;
	}

	protected function getPreviousMonth(): int {
		return date('m', strtotime('-1 day', strtotime($this->getFirstDayOfMonthDate())));
	}

	public function getMonthName(): string {
		$date = date("{$this->year}-{$this->month}-15"); //not working for first on last year for janary
		return Yii::$app->formatter->asDate($date, 'LLLL Y');
	}

	public function getFirstDayOfMonthDate(): string {
		return date("{$this->year}-{$this->month}-01");
	}

	public function getLastDayOfMonthDate(): string {
		return date("{$this->year}-{$this->month}-t");
	}

	/**
	 * @return static[]
	 */
	public function getYearsModels(int $order = SORT_DESC): array {
		$years = $this->getBaseQuery()
			->select([
				'EXTRACT(year FROM created_at) AS year',
				'count(*) AS count',
			])
			->groupBy('EXTRACT(year FROM created_at)')
			->orderBy(['year' => $order])
			->asArray()
			->all($this->db);
		$models = [];
		foreach ($years as $rows) {
			$year = (int) $rows['year'];
			$count = (int) $rows['count'];
			$model = clone $this;
			$model->year = $year;
			if ($this->year !== $year) {
				$model->month = SORT_DESC ? 12 : 1;
			}
			$model->setCountYears($count);
			$models[$year] = $model;
		}
		return $models;
	}

	/**
	 * @return static[]
	 */
	public function getMonthModels(int $order = SORT_DESC): array {
		//	if (!isset(static::$MODELS[$this->year])) {
		$query = $this->getBaseQuery()
			->select([
				'EXTRACT(month FROM created_at) AS month',
				'count(*) AS count',
			])
			->groupBy('EXTRACT(month FROM created_at)')
			->orderBy(['month' => $order])
			->asArray();
		$this->applyYearFilter($query);
		$months = $query->all($this->db);
		$models = [];
		foreach ($months as $rows) {
			$month = (int) $rows['month'];
			$count = (int) $rows['count'];
			$model = clone $this;
			$model->month = $month;
			$model->setCountMonth($count);
			$models[$month] = $model;
		}
		static::$MODELS[$this->year] = $models;
		//}

		return static::$MODELS[$this->year];
	}

	public function getWithSettlements(int $order = SORT_DESC): array {
		if (!isset(static::$MODELS[$this->year])) {
			$query = $this->getBaseQuery()
				->select([
					'EXTRACT(month FROM created_at) AS month',
					'count(*) AS count',
				])
				->groupBy('EXTRACT(month FROM created_at)')
				->orderBy(['month' => $order])
				->asArray();
			$this->applyYearFilter($query);
			$months = $query->all($this->db);
			$models = [];
			foreach ($months as $rows) {
				$month = (int) $rows['month'];
				$count = (int) $rows['count'];
				$model = clone $this;
				$model->month = $month;
				$model->setCountMonth($count);
				$models[$month] = $model;
			}
			static::$MODELS[$this->year] = $models;
		}

		return static::$MODELS[$this->year];
	}

	protected function column(Query $query): array {
		return $query->column($this->db);
	}

	public function getAgentsCountForMonth(int $sort = SORT_DESC): array {
		$query = $this->getBaseQuery();
		$this->applyMonthFilter($query);
		$query->joinWith([
			'users' => function (IssueUserQuery $query): void {
				$query->withType(IssueUser::TYPE_AGENT);
			},
		]);
		$query->select('COUNT(*),user_id')
			->groupBy('user_id')
			->orderBy(['COUNT(*)' => $sort])
			->indexBy('user_id');
		return $this->column($query);
	}

	public function getStagesCountForMonth(int $sort = SORT_DESC): array {
		$query = $this->getBaseQuery();
		$this->applyMonthFilter($query);
		$query->select('COUNT(*)')
			->groupBy('stage_id')
			->orderBy(['COUNT(*)' => $sort])
			->indexBy('stage_id');
		return $this->column($query);
	}

	public function getEntityResponsibleCountForMonth(int $sort = SORT_DESC): array {
		$query = $this->getBaseQuery();
		$this->applyMonthFilter($query);
		$query->select('COUNT(*)')
			->groupBy('entity_responsible_id')
			->orderBy(['COUNT(*)' => $sort])
			->indexBy('entity_responsible_id');
		return $this->column($query);
	}

	public function getAgentPaysSumForMonth(int $sort = SORT_DESC): array {
		$query = $this->getBaseQuery();
		$this->applyMonthFilter($query);
		$query->select([
			'sum(' . IssuePay::tableName() . '.value' . ') as sum',
			'user_id',
		])->joinWith('pays');
		$query->joinWith([
			'users' => function (IssueUserQuery $query): void {
				$query->withType(IssueUser::TYPE_AGENT);
			},
		]);
		$query->groupBy('user_id')
			->orderBy(['sum' => $sort])
			->indexBy('user_id');
		return $this->column($query);
	}

	public function getAgentPaidPaySumForMonth(int $sort = SORT_DESC): array {
		$query = $this->getBaseQuery();
		$this->applyMonthFilter($query);
		$query->select([
			'sum(' . IssuePay::tableName() . '.value' . ') as sum',
			'user_id',
		])->joinWith([
			'pays' => function (IssuePayQuery $query) {
				$query->onlyPaid();
			},
		]);
		$query->joinWith([
			'users' => function (IssueUserQuery $query): void {
				$query->withType(IssueUser::TYPE_AGENT);
			},
		]);
		$query->groupBy('user_id')
			->orderBy(['sum' => $sort])
			->indexBy('user_id');
		return $this->column($query);
	}

	public function getPaysSum(): float {
		$query = $this->getBaseQuery();
		$query->joinWith('pays');
		$this->applyRangeFilter($query, IssuePay::tableName() . '.deadline_at');
		return $this->sum($query, IssuePay::tableName() . '.value');
	}

	public function getPayidPaysSum(): float {
		$query = $this->getBaseQuery();
		$query->joinWith('pays');

		$this->applyRangeFilter($query, IssuePay::tableName() . '.pay_at');

		return $this->sum($query, IssuePay::tableName() . '.value');
	}

	public function getProvisionsSum(): float {
		$query = $this->getBaseQuery();
		$query->joinWith('pays');
		$this->applyRangeFilter($query, IssuePay::tableName() . '.pay_at');
		$query->joinWith('pays.provisions');
		return $this->sum($query, Provision::tableName() . '.value');
	}

	public function getCostsSum() {
		$query = $this->getBaseQuery();
		$query->joinWith('costs');
		$this->applyRangeFilter($query, IssueCost::tableName() . '.date_at');

		return $this->sum($query, IssueCost::tableName() . '.value');
	}

	protected function sum(ActiveQuery $query, string $q): float {
		return (float) $query->sum($q, $this->db);
	}

	public function getAgentDelayedPaySumForMonth(int $sort = SORT_DESC): array {
		$query = $this->getBaseQuery();
		$this->applyMonthFilter($query);
		$query->select([
			'sum(' . IssuePay::tableName() . '.value' . ') as sum',
			'user_id',
		])->joinWith([
			'pays' => function (IssuePayQuery $query) {
				$query->onlyDelayed();
			},
		]);
		$query->joinWith([
			'users' => function (IssueUserQuery $query): void {
				$query->withType(IssueUser::TYPE_AGENT);
			},
		]);
		$query->groupBy('user_id')
			->orderBy(['sum' => $sort])
			->indexBy('user_id');
		return $this->column($query);
	}

	protected function setCountYears(int $count) {
		$this->countYears = $count;
	}

	protected function setCountMonth(int $count) {
		$this->countMonth = $count;
	}

	protected function applyYearFilter(IssueQuery $query): void {
		if ($this->year) {
			$year = $this->year;
			$this->applyCreatedAtFilter(
				$query,
				date("$year-01-01 00:00:00"),
				date("$year-12-t 23:59:59"),
			);
		}
	}

	protected function applyRangeFilter(ActiveQuery $query, string $column): void {
		$query->andWhere([
			'>=', $column, $this->getStartAt(),
		]);
		$query->andWhere([
			'<=', $column, $this->getEndAt(),
		]);
	}

	protected function getStartAt(): string {
		return !empty($this->startAt) ? $this->startAt : date("{$this->year}-{$this->month}-01 00:00:00");
	}

	protected function getEndAt(): string {
		return !empty($this->endAt) ? $this->endAt : date("{$this->year}-{$this->month}-t 23:59:59");
	}

	protected function applyMonthFilter(IssueQuery $query): void {
		$this->applyCreatedAtFilter(
			$query,
			date("{$this->year}-{$this->month}-01 00:00:00"),
			date("{$this->year}-{$this->month}-t 23:59:59"),
		);
	}

	protected function applyCreatedAtFilter(IssueQuery $query, string $start, string $end): void {
		$query->andWhere([
			'>=', Issue::tableName() . '.created_at', $start,
		]);
		$query->andWhere([
			'<=', Issue::tableName() . '.created_at', $end,
		]);
	}

}
