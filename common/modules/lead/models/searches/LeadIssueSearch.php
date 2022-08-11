<?php

namespace common\modules\lead\models\searches;

use common\models\issue\Issue;
use common\models\issue\IssueStage;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCrm;
use common\modules\lead\models\LeadIssue;
use common\modules\lead\models\LeadStatus;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * LeadIssueSearch represents the model behind the search form of `common\modules\lead\models\LeadIssue`.
 */
class LeadIssueSearch extends LeadIssue {

	public $leadName;
	public $leadStatus;

	public $issueStage;

	public $issueDuplicated;

	public ?int $currentCrmAppId = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['lead_id', 'issue_id', 'crm_id', 'leadStatus', 'issueStage'], 'integer'],
			[['issueDuplicated'], 'boolean'],
			[['created_at', 'leadName'], 'safe'],
			[
				'crm_id', 'required', 'when' => function (): bool {
				return (bool) $this->issueDuplicated;
			},
			],
		];
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
				'issueDuplicated' => Yii::t('lead', 'Issue Duplicated'),
				'issueStage' => Yii::t('issue', 'Stage'),
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
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
	public function search($params) {
		$query = LeadIssue::find();
		$query->with('crm');
		$query->with('lead');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			$query->where('0=1');
			return $dataProvider;
		}
		if ($this->isCurrentCrmApp()) {
			//	$query->with('issue');
			//	$query->with('issue.stage');
		}

		$this->applyIssueDuplicatedFilter($query);
		$this->applyIssueStageFilter($query);

		$this->applyLeadNameFilter($query);
		$this->applyLeadStatusFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'lead_id' => $this->lead_id,
			'issue_id' => $this->issue_id,
			'crm_id' => $this->crm_id,
			'created_at' => $this->created_at,
		]);

		return $dataProvider;
	}

	private function applyIssueDuplicatedFilter(ActiveQuery $query): void {
		if ($this->issueDuplicated) {
			$query->addSelect([
				LeadIssue::tableName() . '.*',
				'count(issue_id)',
			])
				->groupBy('issue_id')
				->having('COUNT(issue_id) > 1');
		}
	}

	private function applyIssueStageFilter(ActiveQuery $query): void {
		if ($this->isCurrentCrmApp() && !empty($this->issueStage)) {
			Yii::error($this->issueStage);
			$query->joinWith('issue');
			$query->andWhere([
				Issue::tableName() . '.stage_id' => $this->issueStage,
			]);
		}
	}

	private function applyLeadNameFilter(ActiveQuery $query): void {
		if (!empty($this->leadName)) {
			$query->joinWith('lead');
			$query->andWhere([
				'like',
				Lead::tableName() . '.name',
				$this->leadName,
			]);
		}
	}

	private function applyLeadStatusFilter(ActiveQuery $query): void {
		if (!empty($this->leadStatus)) {
			$query->joinWith('lead');
			$query->andWhere([
				Lead::tableName() . '.status_id' => $this->leadStatus,
			]);
		}
	}

	public static function getCrmsNames(): array {
		return LeadCrm::getNames();
	}

	public static function getLeadStatusesNames(): array {
		return LeadStatus::getNames();
	}

	public function isCurrentCrmApp(): bool {
		return !empty($this->crm_id) && (int) $this->crm_id === $this->currentCrmAppId;
	}

	public function getIssueStagesNames(): ?array {
		if (!$this->isCurrentCrmApp()) {
			return null;
		}
		$stagesIds = Issue::find()
			->select('stage_id')
			->andWhere([
				'id' =>
					LeadIssue::find()
						->select('issue_id')
						->andWhere(['crm_id' => $this->crm_id])
						->column(),
			])
			->distinct()
			->column();

		$names = [];
		foreach ($stagesIds as $stageId) {
			$names[$stageId] = IssueStage::getStagesNames(true)[$stageId];
		}

		return $names;
	}
}
