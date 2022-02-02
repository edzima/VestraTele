<?php

namespace common\models\settlement\search;

use common\models\AgentSearchInterface;
use common\models\issue\IssuePay;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\issue\search\ArchivedIssueSearch;
use common\models\issue\search\IssueTypeSearch;
use common\models\SearchModel;
use common\models\user\CustomerSearchInterface;
use common\models\user\query\UserQuery;
use common\models\user\User;
use Decimal\Decimal;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * IssuePaySearch represents the model behind the search form of `common\models\issue\IssuePay`.
 */
class IssuePaySearch extends IssuePay implements
	AgentSearchInterface,
	ArchivedIssueSearch,
	IssueTypeSearch,
	CustomerSearchInterface,
	SearchModel {

	public const PAY_STATUS_NOT_PAYED = 'not-payed';
	public const PAY_STATUS_PAYED = 'payed';
	public const PAY_STATUS_ALL = 'all';

	public const DELAY_NONE = 'none';
	public const DELAY_MAX_3_DAYS = 'max-3-days';
	public const DELAY_MIN_3_MAX_7_DAYS = 'min-3-max-7-days';
	public const DELAY_MIN_7_MAX_14_DAYS = 'min-7-max-14-days';
	public const DELAY_MIN_14_MAX_30_DAYS = 'min-14-max-30-days';
	public const DELAY_MIN_30_DAYS = 'min-30-days';
	public const DELAY_ALL = 'all';

	public string $payStatus = self::PAY_STATUS_ALL;
	public ?string $delay = self::DELAY_MIN_3_MAX_7_DAYS;

	public ?string $issue_id = null;
	public ?string $calculationOwnerId = null;
	public ?string $customerLastname = null;
	public bool $withArchive = true;

	public $agent_id;

	public $issueTypesIds = [];
	public $calculationType;
	public $deadlineAtFrom;
	public $deadlineAtTo;

	/**
	 * @var int[]
	 */
	public array $agents_ids = [];

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'deadlineAtFrom' => 'Termin od',
			'deadlineAtTo' => 'Termin do',
			'delay' => 'Przeterminowane',
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['issue_id', 'status', 'calculationType'], 'integer'],
			[['delay'], 'string'],
			[['deadlineAtFrom', 'deadlineAtTo'], 'safe'],
			['delay', 'in', 'range' => array_keys(static::getDelaysRangesNames())],
			['status', 'in', 'range' => array_keys(static::getStatusNames())],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			[['value'], 'number'],
			[
				'agent_id', 'in', 'range' => function () {
				return array_keys($this->getAgentsNames());
			}, 'allowArray' => true,
			],
			['issueTypesIds', 'in', 'range' => array_keys(static::getIssueTypesNames()), 'allowArray' => true],
		];
	}

	public function getPayStatus(): string {
		return $this->payStatus;
	}

	public function isNotPayed(): bool {
		return $this->payStatus === static::PAY_STATUS_NOT_PAYED;
	}

	public function isPayed(): bool {
		return $this->payStatus === static::PAY_STATUS_PAYED;
	}

	public static function getPayStatusNames(): array {
		return [
			static::PAY_STATUS_NOT_PAYED => 'Nieopłacone',
			static::PAY_STATUS_PAYED => 'Opłacone',
			static::PAY_STATUS_ALL => 'Wszystkie',
		];
	}

	/**
	 * @inheritdoc
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
		if (!isset(static::getPayStatusNames()[$this->payStatus])) {
			throw new InvalidConfigException('Invalid $payStatus: ' . $this->payStatus);
		}
		$query = IssuePay::find();
		$query->alias('P');
		$query->joinWith('calculation C');
		$query->joinWith([
			'calculation.issue.agent agentUser' => function (UserQuery $query): void {
				$query->joinWith('userProfile AP');
			},
			'calculation.issue.customer customerUser' => function (UserQuery $query): void {
				$query->joinWith('userProfile CP');
			},
		]);
		$query->joinWith('issue.type IT');
		if (!$this->getWithArchive()) {
			$query->joinWith([
				'issue' => function (IssueQuery $query): void {
					$query->withoutArchives();
				},
			]);
		}

		// add conditions that should always apply here
		$this->applyPayStatusFilter($query);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['deadline_at' => SORT_ASC],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			$query->where('0=1');
			return $dataProvider;
		}

		$this->applyAgentsFilters($query);
		$this->applyCustomerSurnameFilter($query);
		$this->applyDelayFilter($query);
		$this->applyIssueTypeFilter($query);

		//	$this->applyStatusFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'P.issue.id' => $this->issue_id,
			'P.deadline_at' => $this->deadline_at,
			'P.transfer_type' => $this->transfer_type,
			'P.value' => $this->value,
			'P.status' => $this->status,
			'C.type' => $this->calculationType,
			'C.owner_id' => $this->calculationOwnerId,
		]);

		$query
			->andFilterWhere(['>=', 'deadline_at', $this->deadlineAtFrom])
			->andFilterWhere(['<=', 'deadline_at', $this->deadlineAtTo]);

		return $dataProvider;
	}

	public function applyAgentsFilters(QueryInterface $query): void {
		if (!empty($this->agent_id)) {
			$query->andWhere(['agent.user_id' => $this->agent_id]);
		}
	}

	public function applyCustomerSurnameFilter(QueryInterface $query): void {
		if (!empty($this->customerLastname)) {
			$query->andWhere(['like', 'CP.lastname', $this->customerLastname . '%', false]);
		}
	}

	private function applyDelayFilter(IssuePayQuery $query): void {
		if ($this->isNotPayed()) {
			switch ($this->delay) {
				case static::DELAY_NONE:
					$query->onlyNotDelayed();
					break;
				case static::DELAY_ALL:
					$query->onlyDelayed();
					break;
				case static::DELAY_MAX_3_DAYS:
					$query->onlyMinDelayed(0);
					$query->onlyMaxDelayed(3);
					break;
				case static::DELAY_MIN_3_MAX_7_DAYS:
					$query->onlyMinDelayed(3);
					$query->onlyMaxDelayed(7);
					break;
				case static::DELAY_MIN_7_MAX_14_DAYS:
					$query->onlyMinDelayed(7);
					$query->onlyMaxDelayed(14);
					break;
				case static::DELAY_MIN_14_MAX_30_DAYS:
					$query->onlyMinDelayed(14);
					$query->onlyMaxDelayed(30);
					break;
				case static::DELAY_MIN_30_DAYS:
					$query->onlyMinDelayed(30);
					break;
			}
		}
	}

	private function applyPayStatusFilter(IssuePayQuery $query): void {
		switch ($this->payStatus) {
			case static::PAY_STATUS_ALL:
				break;
			case static::PAY_STATUS_NOT_PAYED:
				$query->onlyUnpaid();
				break;
			case static::PAY_STATUS_PAYED:
				$query->onlyPaid();
				break;
		}
	}

	protected function applyStatusFilter(QueryInterface $query): void {
		if (empty($this->status)) {
			$query->andWhere(['P.status' => null]);
		} else {
			$query->andWhere(['P.status' => $this->status]);
		}
	}

	public function getPayedSum(IssuePayQuery $query): Decimal {
		return $query->getPayedSum();
	}

	public function getNotPaySum(IssuePayQuery $query): Decimal {
		return $this->getValueSum($query)->sub($query->getPayedSum());
	}

	public function getValueSum(IssuePayQuery $query): Decimal {
		return $query->getValueSum();
	}

	public static function getDelaysRangesNames(): array {
		return [
			static::DELAY_NONE => 'Nie',
			static::DELAY_MAX_3_DAYS => 'do 3 dni',
			static::DELAY_MIN_3_MAX_7_DAYS => '3 - 7 dni',
			static::DELAY_MIN_7_MAX_14_DAYS => '7 - 14 dni',
			static::DELAY_MIN_14_MAX_30_DAYS => '14 - 30 dni',
			static::DELAY_MIN_30_DAYS => '> 30 dni',
			static::DELAY_ALL => 'Wszystkie',
		];
	}

	public function getAgentsNames(): array {
		return User::getSelectList(
			IssueUser::find()
				->select('user_id')
				->withType(IssueUser::TYPE_AGENT)
				->joinWith([
					'issue.payCalculations.pays P' => function (IssuePayQuery $query): void {
						$query->andWhere('P.id is NOT NULL');
						$this->applyPayStatusFilter($query);
						$this->applyDelayFilter($query);
					},
				])
				->andFilterWhere(['user_id' => $this->agents_ids])
				->distinct()
				->column()
			, false);
	}

	public function getWithArchive(): bool {
		return $this->withArchive;
	}

	public function applyIssueTypeFilter(QueryInterface $query): void {
		if (!empty($this->issueTypesIds)) {
			$query->andWhere(['IT.id' => $this->issueTypesIds]);
		}
	}

	public static function getIssueTypesNames(): array {
		return IssueType::getTypesNames();
	}
}
