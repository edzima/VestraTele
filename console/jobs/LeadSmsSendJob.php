<?php

namespace console\jobs;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadSmsForm;
use common\modules\lead\Module;
use console\jobs\exceptions\NotSendSmsException;
use yii\base\InvalidConfigException;

class LeadSmsSendJob extends SmsSendJob {

	public int $lead_id;
	public int $status_id;
	public int $owner_id;

	/**
	 * @throws NotSendSmsException|InvalidConfigException
	 */
	public function run(): string {
		$lead = Module::manager()->findById($this->lead_id);
		if (!$lead) {
			throw new InvalidConfigException('Lead not found: ' . $this->lead_id);
		}
		$smsId = parent::run();
		$this->report($lead, $smsId);
		return $smsId;
	}

	private function report(ActiveLead $lead, string $sms_id): bool {
		$model = new LeadSmsForm($lead);
		$model->owner_id = $this->owner_id;
		$model->status_id = $this->status_id;
		$model->message = $this->message->getMessage();
		return $model->report($sms_id);
	}
}
