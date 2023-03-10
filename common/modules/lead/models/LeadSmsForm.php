<?php

namespace common\modules\lead\models;

use common\components\message\MessageTemplateKeyHelper;
use common\models\message\QueueSmsForm;
use common\modules\lead\models\forms\ReportForm;
use console\jobs\LeadSmsSendJob;
use Edzima\Yii2Adescom\models\MessageInterface;
use Yii;
use yii\validators\CompareValidator;

class LeadSmsForm extends QueueSmsForm {

	protected const KEY_ISSUE_TYPES = 'type';

	public const SCENARIO_CHANGE_STATUS = 'change-status';

	private ActiveLead $lead;
	public int $status_id;
	public ?int $owner_id = null;

	public function __construct(ActiveLead $lead, $config = []) {
		$this->lead = $lead;
		$this->status_id = $lead->getStatusId();
		$this->phone = $lead->getPhone();
		parent::__construct($config);
	}

	public static function isForLeadType(string $key, int $id): bool {
		$ids = (array) static::getTypesIds($key);
		return empty($ids)
			|| in_array($id, $ids);
	}

	protected static function getTypesIds(string $key) {
		return MessageTemplateKeyHelper::getValue($key, static::KEY_ISSUE_TYPES);
	}

	public function attributeLabels(): array {
		return array_merge(
			parent::attributeLabels(), [
			'status_id' => Yii::t('lead', 'Status'),
		]);
	}

	public function rules(): array {
		return array_merge(
			[
				[['!owner_id', 'status_id'], 'required'],
				['status_id', 'integer'],
				['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
				[
					'status_id',
					'compare',
					'on' => static::SCENARIO_CHANGE_STATUS,
					'type' => CompareValidator::TYPE_NUMBER,
					'operator' => '!==',
					'compareValue' => $this->getLead()->getStatusId(),
					'message' => Yii::t('lead', 'Status cannot be current Status: {status}', [
						'status' => static::getStatusNames()[$this->getLead()->getStatusId()],
					]),
				],
			],
			parent::rules()
		);
	}

	public function getLead(): ActiveLead {
		return $this->lead;
	}

	public function report(string $smsId): bool {
		$report = new ReportForm();
		$report->withSameContacts = false;
		$report->setLead($this->lead);
		$report->owner_id = $this->owner_id;
		$report->status_id = $this->status_id;
		$report->details = static::detailsPrefix() . $this->getMessage()->getMessage() . ' - SMS_ID: ' . $smsId;
		if ($report->save()) {
			return true;
		}
		Yii::error($report->getErrors(), __METHOD__);
		return false;
	}

	public static function detailsPrefix(): string {
		return Yii::t('common', 'SMS Sent: ');
	}

	protected function createJob(MessageInterface $message = null): LeadSmsSendJob {
		if ($message === null) {
			$message = $this->getMessage();
		}
		return new LeadSmsSendJob([
			'lead_id' => $this->lead->getId(),
			'message' => $message,
			'status_id' => $this->status_id,
			'owner_id' => $this->owner_id,
		]);
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

}
