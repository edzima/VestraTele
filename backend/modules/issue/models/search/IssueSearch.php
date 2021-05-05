<?php

namespace backend\modules\issue\models\search;

use common\models\AddressSearch;
use common\models\issue\Issue;
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

	public $parentId;
	public $accident_at;
	public $excludedStages = [];
	public bool $onlyDelayed = false;
	public bool $onlyWithPayedPay = false;

	public function __construct($config = []) {
		if (!isset($config['addressSearch'])) {
			$config['addressSearch'] = new AddressSearch();
		}
		parent::__construct($config);
	}

	public function rules(): array {
		return array_merge(parent::rules(), [
			[['parentId', 'agent_id', 'tele_id', 'lawyer_id',], 'integer'],
			[['onlyDelayed', 'onlyWithPayedPay'], 'boolean'],
			['accident_at', 'safe'],
			['excludedStages', 'in', 'range' => array_keys($this->getStagesNames()), 'allowArray' => true],
		]);
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'parentId' => Yii::t('backend', 'Structures'),
			'excludedStages' => Yii::t('backend', 'Excluded stages'),
			'onlyDelayed' => Yii::t('backend', 'Only delayed'),
			'onlyWithPayedPay' => Yii::t('backend', 'Only with payed pay'),
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

	protected function issueQueryFilter(IssueQuery $query): void {
		parent::issueQueryFilter($query);
		$this->delayedFilter($query);
		$this->excludedStagesFilter($query);
		$this->teleFilter($query);
		$this->lawyerFilter($query);
		$this->payedFilter($query);
		$query->andFilterWhere(['accident_at' => $this->accident_at]);
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
							'stage_id' => array_keys($ids),
						],
						[
							'<=', new Expression("DATE_ADD(stage_change_at, INTERVAL $day DAY)"), new Expression('NOW()'),
						],
					]);
				}
			}
			$query->andWhere('stage_change_at IS NOT NULL');
			$query->andWhere('issue_stage.days_reminder is NOT NULL');
		}
	}

	protected function excludedStagesFilter(IssueQuery $query): void {
		$query->andFilterWhere(['NOT IN', 'stage_id', $this->excludedStages]);
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

	public function getAgentsNames(): array {
		if (empty($this->parentId) || $this->parentId < 0) {
			return parent::getAgentsNames();
		}
		$ids = Yii::$app->userHierarchy->getAllChildesIds($this->parentId);
		$ids[] = $this->parentId;
		return User::getSelectList($ids, false);
	}

	private function payedFilter(IssueQuery $query): void {
		if ($this->onlyWithPayedPay) {
			$query->joinWith([
				'pays' => function (IssuePayQuery $payQuery) {
					$payQuery->onlyPayed();
				},
			]);
		}
	}

}
