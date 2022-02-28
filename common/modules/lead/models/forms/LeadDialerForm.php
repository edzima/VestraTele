<?php

namespace common\modules\lead\models\forms;

use common\helpers\ArrayHelper;
use common\modules\lead\entities\DialerConfig;
use common\modules\lead\entities\DialerConfigInterface;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\LeadDialerType;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

class LeadDialerForm extends Model implements DialerConfigInterface {

	public $leadId;
	public $typeId;
	public $priority;
	public $status;

	public $dailyAttemptsLimit;
	public $globallyAttemptsLimit;
	public $nextCallInterval;

	private ?LeadDialer $model = null;

	public function rules(): array {
		return [
			[['leadId', 'typeId', 'priority', 'nextCallInterval', 'dailyAttemptsLimit', 'globallyAttemptsLimit'], 'required'],
			[
				['leadId', 'typeId', 'priority', 'nextCallInterval', 'globallyAttemptsLimit', 'dailyAttemptsLimit'], 'integer',
			],
			['typeId', 'in', 'range' => array_keys(static::getTypesNames())],
			['priority', 'in', 'range' => array_keys(static::getPriorityNames())],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
			[
				'leadId', 'exist', 'targetClass' => Lead::class, 'targetAttribute' => ['leadId' => 'id'],
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'leadId' => Yii::t('lead', 'Lead'),
			'typeId' => Yii::t('lead', 'Type'),
			'priority' => Yii::t('lead', 'Priority'),
			'dailyAttemptsLimit' => Yii::t('lead', 'Daily attempts limit'),
			'globallyAttemptsLimit' => Yii::t('lead', 'Globally attempts limit'),
			'nextCallInterval' => Yii::t('lead', 'Next call interval'),
		];
	}

	public function setConfig(DialerConfigInterface $config): void {
		$this->dailyAttemptsLimit = $config->getDailyAttemptsLimit();
		$this->globallyAttemptsLimit = $config->getGloballyAttemptsLimit();
		$this->nextCallInterval = $config->getNextCallInterval();
	}

	public function getModel(): LeadDialer {
		if ($this->model === null) {
			$this->model = new LeadDialer();
		}
		return $this->model;
	}

	public function setModel(LeadDialer $model): void {
		$this->model = $model;
		$this->priority = $model->priority;
		$this->status = $model->status;
		$this->leadId = $model->lead_id;
		$this->typeId = $model->type_id;
		$this->setConfig($this->model->getConfig());
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->lead_id = $this->leadId;
		$model->type_id = $this->typeId;
		$model->status = $this->status;
		$model->priority = $this->priority;
		$model->dialer_config = Json::encode($this->getDialerConfig()->toArray());
		return $model->save(false);
	}

	public function getDailyAttemptsLimit(): int {
		return $this->dailyAttemptsLimit;
	}

	public function getGloballyAttemptsLimit(): int {
		return $this->globallyAttemptsLimit;
	}

	public function getNextCallInterval(): int {
		return $this->nextCallInterval;
	}

	public function getDialerConfig(): DialerConfig {
		return DialerConfig::fromConfig($this);
	}

	public static function getPriorityNames(): array {
		return LeadDialer::getPriorityNames();
	}

	public static function getStatusesNames(): array {
		return LeadDialer::getStatusesNames();
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(
			LeadDialerType::find()->asArray()->all(),
			'id',
			'name'
		);
	}

}
