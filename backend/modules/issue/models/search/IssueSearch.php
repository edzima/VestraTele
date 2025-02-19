<?php

namespace backend\modules\issue\models\search;

use common\models\issue\Issue;
use common\models\issue\IssueClaim;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSearch as BaseIssueSearch;
use common\models\issue\IssueUser;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\user\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\QueryInterface;

/**
 * IssueSearch model for backend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueSearch extends BaseIssueSearch {

	public const SCENARIO_ALL_PAYED = 'allPayed';

	public $parentId;
	public $onlyWithSettlements;

	public $onlyWithClaims;
	public ?string $claimCompanyTryingValue = null;

	public bool $onlyDelayed = false;
	public bool $onlyWithPayedPay = false;
	public bool $onlyWithAllPayedPay = false;
	public bool $withArchiveOnAllPayedPay = false;

	public $stage_change_at;

	public $stageDeadlineFromAt;
	public $stageDeadlineToAt;

	public $entity_agreement_details;
	public $entity_agreement_at;

	public $groupByIssueUserTypes;

	public bool $withClaimsSum = false;

	public ?string $signature_act = null;
	/**
	 * @var mixed|null
	 */
	public bool $showChart = false;

	private ?array $ids = null;

	private bool $isLoad = false;

	public function rules(): array {
		return array_merge(parent::rules(), [
			['!userId', 'required'],
			[['parentId', 'agent_id', 'tele_id', 'lawyer_id'], 'integer'],
			[['onlyDelayed', 'onlyWithPayedPay', 'onlyWithSettlements', 'onlyWithClaims', 'withClaimsSum', 'showChart'], 'boolean'],
			[['entity_agreement_details'], 'string'],
			[['onlyWithSettlements', 'onlyWithClaims'], 'default', 'value' => null],
			['claimCompanyTryingValue', 'number', 'min' => 0],
			['onlyWithAllPayedPay', 'boolean', 'on' => static::SCENARIO_ALL_PAYED],
			[['signature_act', 'stage_change_at', 'stageDeadlineFromAt', 'stageDeadlineToAt', 'entity_agreement_at'], 'safe'],
			[['groupByIssueUserTypes', 'withoutUserTypes'], 'in', 'range' => array_keys(static::getIssueUserTypesNames()), 'allowArray' => true],
		]);
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'parentId' => Yii::t('backend', 'Structures'),
			'onlyDelayed' => Yii::t('backend', 'Only delayed'),
			'onlyWithClaims' => Yii::t('backend', 'Only with Claims'),
			'onlyWithPayedPay' => Yii::t('backend', 'Only with payed pay'),
			'onlyWithSettlements' => Yii::t('settlement', 'Only with Settlements'),
			'onlyWithAllPayedPay' => Yii::t('settlement', 'Only with all paid Pays'),
			'stageDeadlineFromAt' => Yii::t('backend', 'Stage Deadline from at'),
			'stageDeadlineToAt' => Yii::t('backend', 'Stage Deadline to at'),
			'withClaimsSum' => Yii::t('backend', 'With claims sum'),
			'groupByIssueUserTypes' => Yii::t('backend', 'Group by Issue user Types'),
			'withoutUserTypes' => Yii::t('backend', 'Without user types'),
		]);
	}

	public function load($data, $formName = null) {
		$this->isLoad = parent::load($data, $formName);
		return $this->isLoad;
	}

	public function getIsLoad(): bool {
		return $this->isLoad;
	}

	public function search(array $params): ActiveDataProvider {
		$query = Issue::find();

		$query->with($this->issueWith());

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'updated_at' => SORT_DESC,
				],
			],
		]);

		if ($this->scenario === static::SCENARIO_ALL_PAYED) {
			$query->with('pays');
		}
		$this->onlyUserTypes($query);

		$this->load($params);
		if ($this->addressSearch) {
			$this->addressSearch->load($params);
		}

		if (!$this->validate()) {
			if ($this->isArchiveScenario()) {
				$query->andWhere('0=1');
			}
			$this->archiveFilter($query);
			return $dataProvider;
		}
		$this->issueQueryFilter($query);

		$dataProvider->sort->attributes['claimCompanyTryingValue'] = [
			'asc' => ['trying_value' => SORT_ASC],
			'desc' => ['trying_value' => SORT_DESC],
		];

		return $dataProvider;
	}

	public function isArchiveScenario(): bool {
		return $this->scenario === static::SCENARIO_ARCHIVE_CUSTOMER;
	}

	protected function archiveFilter(IssueQuery $query): void {
		parent::archiveFilter($query);
		if ($this->onlyWithAllPayedPay && !$this->withArchiveOnAllPayedPay) {
			$query->withoutArchives();
		}
	}

	protected function issueQueryFilter(IssueQuery $query): void {
		parent::issueQueryFilter($query);
		$this->signatureActFilter($query);
		$this->delayedFilter($query);
		$this->teleFilter($query);
		$this->lawyerFilter($query);
		$this->payedFilter($query);
		$this->settlementsFilter($query);
		$this->stageChangeAtFilter($query);
		$this->applyStageDeadlineFilter($query);
		$this->claimFilter($query);
		$this->applyEntityAgreementFilter($query);
		$this->applyGroupByIssueUserTypes($query);
		$this->applyWithoutUserTypes($query);
	}

	private function signatureActFilter(IssueQuery $query): void {
		$query->andFilterWhere(['like', Issue::tableName() . '.signature_act', $this->signature_act]);
	}

	public function applyAgentsFilters(QueryInterface $query): void {
		$ids = [];
		if (!empty($this->agent_id)) {
			$ids[] = $this->agent_id;
		} elseif (!empty($this->parentId)) {
			$ids = Yii::$app->userHierarchy->getAllChildesIds($this->parentId);
			$ids[] = $this->parentId;
		}
		/** @var IssueQuery $query */
		$query->agents($ids);
	}

	private function delayedFilter(IssueQuery $query): void {
		if (!empty($this->onlyDelayed)) {
			$query->andWhere('stage_deadline_at IS NOT NULL');
			$query->andWhere([
				'<=',
				'stage_deadline_at',
				new Expression('NOW()'),
			]);
		}
	}

	protected function lawyerFilter(IssueQuery $query): void {
		if (!empty($this->lawyer_id)) {
			$query->lawyers([$this->lawyer_id]);
		}
	}

	protected function teleFilter(IssueQuery $query): void {
		if (!empty($this->tele_id)) {
			$query->tele([$this->tele_id]);
		}
	}

	private function payedFilter(IssueQuery $query): void {
		if ($this->onlyWithPayedPay || $this->onlyWithAllPayedPay) {
			$query->joinWith([
				'pays P' => function (IssuePayQuery $payQuery) {
					if ($this->onlyWithPayedPay && !$this->onlyWithAllPayedPay) {
						$payQuery->onlyPaid();
					}
				},
			])
				->groupBy(IssuePayCalculation::tableName() . '.issue_id');
			if ($this->onlyWithAllPayedPay) {
				$query->select(['issue.*', new Expression('SUM(ISNULL(P.pay_at)) as notPayedCount')]);
				$query->having('notPayedCount = 0');
			}
		}
	}

	private function settlementsFilter(IssueQuery $query): void {
		if ($this->onlyWithSettlements === null || $this->onlyWithSettlements === '') {
			return;
		}
		$query->joinWith('payCalculations PC', false);
		if ((bool) $this->onlyWithSettlements === true) {

			$query->andWhere('PC.issue_id IS NOT NULL');
			return;
		}
		$query->andWhere('PC.issue_id IS NULL');
	}

	public function getAgentsNames(): array {
		if (empty($this->parentId) || $this->parentId < 0) {
			return parent::getAgentsNames();
		}
		$ids = Yii::$app->userHierarchy->getAllChildesIds($this->parentId);
		$ids[] = $this->parentId;
		return User::getSelectList($ids, false);
	}

	private function stageChangeAtFilter(IssueQuery $query): void {
		if (!empty($this->stage_change_at)) {
			$query->andWhere(['>=', Issue::tableName() . '.stage_change_at', date('Y-m-d 00:00:00', strtotime($this->stage_change_at))]);
			$query->andWhere(['<=', Issue::tableName() . '.stage_change_at', date('Y-m-d 23:59:59', strtotime($this->stage_change_at))]);
		}
	}

	private function claimFilter(IssueQuery $query): void {
		if (!empty($this->claimCompanyTryingValue)) {
			$query->joinWith('claims');
			$query->andWhere([
				IssueClaim::tableName() . '.type' => IssueClaim::TYPE_COMPANY,
			]);
			$query->andWhere(['like', IssueClaim::tableName() . '.trying_value', $this->claimCompanyTryingValue]);
		}

		if ($this->onlyWithClaims === null || $this->onlyWithClaims === '') {
			return;
		}
		$query->joinWith('claims', false);
		if ((bool) $this->onlyWithClaims === true) {
			$query->andWhere(IssueClaim::tableName() . '.trying_value IS NOT NULL');
			return;
		}
		$query->andWhere(IssueClaim::tableName() . '.issue_id IS NULL');
	}

	private function applyStageDeadlineFilter(IssueQuery $query): void {
		if (!empty($this->stageDeadlineFromAt)) {
			$query->andWhere(['>', Issue::tableName() . '.stage_deadline_at', date('Y-m-d 00:00:00', strtotime($this->stageDeadlineFromAt))]);
		}
		if (!empty($this->stageDeadlineToAt)) {
			$query->andWhere(['<', Issue::tableName() . '.stage_deadline_at', date('Y-m-d 23:59:59', strtotime($this->stageDeadlineToAt))]);
		}
	}

	private function applyEntityAgreementFilter(IssueQuery $query) {
		$query->andFilterWhere(['like', Issue::tableName() . '.entity_agreement_details', $this->entity_agreement_details])
			->andFilterWhere([Issue::tableName() . '.entity_agreement_at' => $this->entity_agreement_at]);
	}

	public function claimsSum(IssueQuery $query) {
		$query = clone $query;
		$query->select(IssueClaim::tableName() . '.trying_value');
		$query->joinWith('claims');
		return $query->sum('trying_value');
	}

	private function applyGroupByIssueUserTypes(IssueQuery $query): void {
		if (!empty($this->groupByIssueUserTypes)) {
			$query->joinWith('users');
			$query->groupBy([IssueUser::tableName() . '.user_id', IssueUser::tableName() . '.type']);
			$query->having([IssueUser::tableName() . '.type' => $this->groupByIssueUserTypes]);
		}
	}

	public $withoutUserTypes = [];

	private function applyWithoutUserTypes(IssueQuery $query): void {
		if (!empty($this->withoutUserTypes)) {
			$subQuery = IssueUser::find()
				->select('issue_id')
				->andWhere([
					'NOT IN', 'issue_id',
					IssueUser::find()
						->select('issue_id')
						->andWhere(['type' => $this->withoutUserTypes]),
				]);

			$query->andWhere([
				'IN', Issue::tableName() . '.id', $subQuery,
			]);
		}
	}

}
