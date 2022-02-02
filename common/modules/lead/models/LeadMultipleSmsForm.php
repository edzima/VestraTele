<?php

namespace common\modules\lead\models;

use common\models\message\QueueSmsForm;
use console\jobs\SmsSendJob;
use Edzima\Yii2Adescom\models\MessageInterface;
use Yii;
use yii\base\InvalidCallException;
use yii\helpers\ArrayHelper;

class LeadMultipleSmsForm extends QueueSmsForm {

	public const SCENARIO_DEFAULT = self::SCENARIO_MULTIPLE;

	public ?int $owner_id = null;
	public ?int $status_id = null;
	public array $ids = [];
	public array $models = [];

	public function rules(): array {
		return array_merge([
			[['status_id', '!owner_id'], 'required'],
			[
				'ids', 'required', 'when' => function (): bool {
				return empty($this->models);
			}, 'message' => Yii::t('lead', 'Ids cannot be blank when Models are empty.'),
			],
			[
				'models', 'required', 'when' => function (): bool {
				return empty($this->ids);
			},
				'message' => Yii::t('lead', 'Models cannot be blank when Ids are empty.'),
			],
			['status_id', 'in', 'range' => array_keys(static::getStatusNames())],
			['!phones', 'default', 'value' => $this->getPhones()],
		],
			parent::rules(),
		);
	}

	public function attributeLabels(): array {
		return array_merge([
			'owner_id' => Yii::t('lead', 'Owner'),
			'status_id' => Yii::t('lead', 'Status'),
		],
			parent::attributeLabels()
		);
	}

	public function getPhones(): array {
		return ArrayHelper::map($this->getModels(), 'id', 'phone');
	}

	/**
	 * @return ActiveLead[]
	 */
	public function getModels(): array {
		if (empty($this->models)) {
			$this->models = Lead::find()
				->andWhere(['id' => $this->ids])
				->andWhere('phone IS NOT NULL')
				->all();
		} else {
			$this->models = $this->onlyPhones($this->models);
		}
		return $this->models;
	}

	public function onlyPhones(array $models): array {
		return array_filter($models, static function (ActiveLead $lead) {
			return !empty($lead->getPhone());
		});
	}

	public function pushJobs(): ?array {
		if (!$this->validate(['message', 'status_id', 'withOverwrite', 'removeSpecialCharacters'])) {
			Yii::error($this->getErrors());
			return null;
		}
		$ids = [];
		foreach ($this->getModels() as $model) {
			$sms = $this->createLeadSms($model);
			$ids[] = $sms->pushJob();
		}
		return $ids;
	}

	protected function createLeadSms(ActiveLead $lead): LeadSmsForm {
		$model = new LeadSmsForm($lead);
		$model->message = $this->message;
		$model->withOverwrite = $this->withOverwrite;
		$model->removeSpecialCharacters = $this->removeSpecialCharacters;
		$model->queue = $this->queue;
		$model->owner_id = $this->owner_id;
		$model->status_id = $this->status_id;
		return $model;
	}

	protected function createJob(MessageInterface $message = null, ActiveLead $lead = null): SmsSendJob {
		throw new InvalidCallException('Method NotAllowed');
	}

	public static function getStatusNames(): array {
		return LeadSmsForm::getStatusNames();
	}
}
