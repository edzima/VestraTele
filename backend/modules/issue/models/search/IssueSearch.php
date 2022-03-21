<?php

namespace backend\modules\issue\models\search;

use backend\modules\issue\models\IssueStage;
use common\models\AddressSearch;
use common\models\issue\Issue;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSearch as BaseIssueSearch;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\user\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;

/**
 * IssueSearch model for backend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueSearch extends BaseIssueSearch {

	public const SCENARIO_ALL_PAYED = 'allPayed';

	public $parentId;
	public $excludedStages = [];
	public $onlyWithSettlements;

	public bool $onlyDelayed = false;
	public bool $onlyWithPayedPay = false;
	public bool $onlyWithAllPayedPay = false;
	public bool $withArchiveOnAllPayedPay = false;

	public ?string $signature_act = null;
	private ?array $ids = null;

	public function __construct($config = []) {
		if (!isset($config['addressSearch'])) {
			$config['addressSearch'] = new AddressSearch();
		}
		parent::__construct($config);
	}

	public function rules(): array {
		return array_merge(parent::rules(), [
			[['parentId', 'agent_id', 'tele_id', 'lawyer_id',], 'integer'],
			[['onlyDelayed', 'onlyWithPayedPay', 'onlyWithSettlements'], 'boolean'],
			['onlyWithAllPayedPay', 'boolean', 'on' => static::SCENARIO_ALL_PAYED],
			[['type_additional_date_at', 'signature_act'], 'safe'],
			['excludedStages', 'in', 'range' => array_keys($this->getStagesNames()), 'allowArray' => true],
		]);
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'parentId' => Yii::t('backend', 'Structures'),
			'excludedStages' => Yii::t('backend', 'Excluded stages'),
			'onlyDelayed' => Yii::t('backend', 'Only delayed'),
			'onlyWithPayedPay' => Yii::t('backend', 'Only with payed pay'),
			'onlyWithSettlements' => Yii::t('settlement', 'Only with Settlements'),
			'onlyWithAllPayedPay' => Yii::t('settlement', 'Only with all paid Pays'),
		]);
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

		$this->load($params);
		if ($this->addressSearch) {
			$this->addressSearch->load($params);
		}

		if (!$this->validate()) {
			$this->archiveFilter($query);
			return $dataProvider;
		}
		$this->issueQueryFilter($query);

		return $dataProvider;
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
		$this->excludedStagesFilter($query);
		$this->teleFilter($query);
		$this->lawyerFilter($query);
		$this->payedFilter($query);
		$this->settlementsFilter($query);
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
			$query->joinWith('stage');
			$daysGroups = ArrayHelper::map($this->getStagesNames(), 'id', 'days_reminder', 'days_reminder');

			foreach ($daysGroups as $day => $ids) {
				if (!empty($day)) {
					$query->orFilterWhere([
						'and',
						[
							Issue::tableName() . '.stage_id' => array_keys($ids),
						],
						[
							'<=', new Expression("DATE_ADD(stage_change_at, INTERVAL $day DAY)"), new Expression('NOW()'),
						],
					]);
				}
			}
			$query->andWhere(Issue::tableName() . '.stage_change_at IS NOT NULL');
			$query->andWhere(IssueStage::tableName() . '.days_reminder is NOT NULL');
		}
	}

	protected function excludedStagesFilter(IssueQuery $query): void {
		$query->andFilterWhere(['NOT IN', Issue::tableName() . '.stage_id', $this->excludedStages]);
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

	public function getAllIds(QueryInterface $query, bool $refresh = false): array {
		if ($refresh || $this->ids === null) {
			$query = clone $query;
			$query->select(Issue::tableName() . '.id');
			$this->payedFilter($query);
			$this->ids = $query->column();
		}
		return $this->ids;
	}

}
