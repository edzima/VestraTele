<?php

namespace common\components;

use common\models\issue\form\IssueLeadPhone;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\KeyStorageItem;
use common\models\user\User as UserModel;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadIssue;
use common\modules\lead\models\query\LeadQuery;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

class IssuesLeads extends Component {

	protected const LOG_CATEGORY = 'IssueLeads';

	public ?int $crmId = null;

	public $modelPhone = [
		'class' => IssueLeadPhone::class,
	];

	public function userLeads(UserModel $user): ?LeadQuery {
		$model = $this->createPhoneModel();
		$model->phone = $model::getUserPhones($user);
		if ($model->validate()) {
			return $model->findLeads();
		}
		return null;
	}

	public function issueLeads(IssueInterface $issue): ?LeadQuery {
		$model = $this->createPhoneModel();
		$model->setIssue($issue);
		if ($model->validate()) {
			return $model->findLeads();
		}
		return null;
	}

	public function linkedLeads(int $id): ActiveQuery {
		return LeadIssue::find()
			->andWhere([
				'crm_id' => $this->getCrmId(),
				'issue_id' => $id,
			]);
	}

	public function mergeNotLinkedIssues(): ?int {
		$crmId = $this->getCrmId();
		$leadsIssues = LeadIssue::find()
			->select('issue_id')
			->andWhere([
				'crm_id' => $crmId,
			])
			->column();
		$rows = [];
		$stats = [];
		Yii::info('Issues Already Linked: ' . count($leadsIssues), static::LOG_CATEGORY . ':' . __FUNCTION__);
		foreach (Issue::find()
			->andFilterWhere([
					'NOT IN', 'id', $leadsIssues,
				]
			)
			->with('users.user.userProfile')
			->batch() as $issues) {
			foreach ($issues as $issue) {
				/** @var Issue $issue */
				$leadsQuery = $this->issueLeads($issue);
				if ($leadsQuery !== null) {
					$leads = $leadsQuery->all();
					if (!empty($leads)) {
						Yii::info('Issue:  ' . $issue->getIssueName() . ' has Leads: ' . count($leads), static::LOG_CATEGORY . ':' . __FUNCTION__);

						if (count($leads) === 1) {
							$stats['single'][] = $issue->getIssueId();

							/** @var ActiveLead $lead */
							$lead = reset($leads);
							$rows[] = [
								'crm_id' => $crmId,
								'issue_id' => $issue->getIssueId(),
								'lead_id' => $lead->getId(),
								'confirmed_at' => $this->getConfirmedAt($lead),
							];
						} else {
							$stats['multiple'][count($leads)][] = $issue->getIssueId();
							foreach ($leads as $lead) {
								$rows[] = [
									'crm_id' => $crmId,
									'issue_id' => $issue->getIssueId(),
									'lead_id' => $lead->getId(),
									'confirmed_at' => null,
								];
							}
						}
					} else {
						$stats['withoutLeads'][] = $issue->getIssueId();
					}
				}
			}
		}
		Yii::info($stats, static::LOG_CATEGORY . ':' . __FUNCTION__);
		if (!empty($rows)) {
			return LeadIssue::getDb()->createCommand()
				->batchInsert(LeadIssue::tableName(), [
					'crm_id',
					'issue_id',
					'lead_id',
					'confirmed_at',
				], $rows)->execute();
		}
		Yii::info('Issue not find to Linked with Leads.', static::LOG_CATEGORY . ':' . __FUNCTION__);

		return null;
	}

	private function getConfirmedAt(ActiveLead $lead): string {
		if (empty($lead->reports)) {
			return $lead->getDateTime()->format(DATE_ATOM);
		}
		$date = [];
		foreach ($lead->reports as $report) {
			$date[] = $report->updated_at;
		}
		return max($date);
	}

	public function getCrmId(): int {
		if ($this->crmId === null) {
			$this->crmId = Yii::$app->keyStorage->get(KeyStorageItem::KEY_LEAD_CRM_ID);
		}
		if ($this->crmId === null) {
			throw new InvalidConfigException('Lead CRM App Id must set.');
		}
		return $this->crmId;
	}

	public function createPhoneModel(): IssueLeadPhone {
		$config = $this->modelPhone;
		if (!isset($config['class'])) {
			$config['class'] = IssueLeadPhone::class;
		}
		return Yii::createObject($config);
	}
}
