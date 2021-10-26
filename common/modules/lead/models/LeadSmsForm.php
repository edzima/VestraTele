<?php

namespace common\modules\lead\models;

use common\modules\lead\models\forms\ReportForm;
use console\jobs\LeadSmsSendJob;
use Edzima\Yii2Adescom\models\SmsForm;
use Yii;
use yii\validators\CompareValidator;

class LeadSmsForm extends SmsForm {

	private ActiveLead $lead;
	public int $status_id;
	public ?int $owner_id = null;

	public function __construct(ActiveLead $lead, $config = []) {
		$this->lead = $lead;
		$this->status_id = $lead->getStatusId();
		$this->phone = $lead->getPhone();
		parent::__construct($config);
	}

	public function rules(): array {
		return array_merge(
			[
				[['!owner_id', 'status_id'], 'required'],
				['status_id', 'integer'],
				[
					'status_id',
					'compare',
					'type' => CompareValidator::TYPE_NUMBER,
					'operator' => '!==',
					'compareValue' => $this->getLead()->getStatusId(),
					'message' => Yii::t('lead', 'Status cannot be current Status: {status}', [
						'status' => LeadStatus::getNames()[$this->getLead()->getStatusId()],
					]),
				],
			],
			parent::rules()
		);
	}

	public function getLead(): ActiveLead {
		return $this->lead;
	}

	public function pushJob(): ?string {
		if (!$this->validate()) {
			return null;
		}
		return Yii::$app->queue->push($this->createJob());
	}

	public function report(string $smsId): bool {
		$report = new ReportForm();
		$report->setLead($this->lead);
		$report->owner_id = $this->owner_id;
		$report->status_id = $this->status_id;
		$report->details = Yii::t('common', 'SMS Sent: ') . $this->getMessage()->getMessage() . ' - SMS_ID: ' . $smsId;
		if ($report->save()) {
			return true;
		}
		Yii::error($report->getErrors(), __METHOD__);
		return false;
	}

	protected function createJob(): LeadSmsSendJob {
		return new LeadSmsSendJob([
			'lead_id' => $this->lead->getId(),
			'message' => $this->getMessage(),
			'status_id' => $this->status_id,
			'owner_id' => $this->owner_id,
		]);
	}

}
