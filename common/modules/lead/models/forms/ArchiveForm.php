<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatusInterface;
use common\modules\lead\Module;
use Yii;
use yii\base\Model;

class ArchiveForm extends Model {

	public $userId;
	public bool $selfChange = false;
	public bool $withSameContacts = true;
	public bool $withSameContactWithType = true;

	private ActiveLead $lead;

	public function rules(): array {
		return [
			[['userId', 'selfChange', 'withSameContacts', 'withSameContactWithType'], 'required'],
			[['selfChange', 'withSameContacts', 'withSameContactWithType'], 'boolean'],
			['userId', 'exist', 'targetClass' => Module::userClass(), 'targetAttribute' => ['userId' => 'id']],
		];
	}

	public function setLead(ActiveLead $lead): void {
		$this->lead = $lead;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		if ($this->selfChange) {
			$this->changeStatusWithReport($this->lead);
		}
		if ($this->withSameContacts) {
			$leads = $this->lead->getSameContacts($this->withSameContactWithType);
			foreach ($leads as $lead) {
				$this->changeStatusWithReport($lead);
			}
		}
		return true;
	}

	private function changeStatusWithReport(ActiveLead $lead): bool {
		if ($lead->getStatusId() === LeadStatusInterface::STATUS_ARCHIVE) {
			return false;
		}
		$report = new LeadReport();
		$report->lead_id = $lead->getId();
		$report->old_status_id = $lead->getStatusId();
		$report->status_id = LeadStatusInterface::STATUS_ARCHIVE;
		$report->owner_id = $this->userId;
		if ($this->lead->getId() !== $lead->getId()) {

			$report->details = Yii::t('lead', 'Move to Archive from Same Lead: {lead}', [
				'lead' => $this->lead->getId(),
			]);
		}
		return $report->save(false) && $lead->updateStatus(LeadStatusInterface::STATUS_ARCHIVE);
	}
}
