<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use Yii;
use yii\base\Model;

class LeadDeadlineForm extends Model {

	public $deadlineAt;

	private Lead $lead;

	public function rules(): array {
		return [
			['deadlineAt', 'date', 'format' => 'Y-m-d'],
			['deadlineAt', 'default', 'value' => null],
		];
	}

	public function attributeLabels(): array {
		return [
			'deadlineAt' => Yii::t('lead', 'Deadline'),
		];
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getLead();
		$model->updateAttributes(['deadline_at' => $this->deadlineAt]);
		return true;
	}

	public function getLead(): ActiveLead {
		return $this->lead;
	}

	public function setLead(Lead $lead) {
		$this->lead = $lead;
		$this->deadlineAt = $lead->getDeadline();
	}

}
