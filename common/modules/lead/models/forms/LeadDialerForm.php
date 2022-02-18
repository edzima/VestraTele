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

	public $dailyAttemptsLimit;
	public $globallyAttemptsLimit;
	public $nextCallInterval;

	private ?LeadDialer $model = null;
	public ?DialerConfig $config = null;

	public function rules(): array {
		return [
			[['leadId', 'typeId', 'priority', 'nextCallInterval', 'dailyAttemptsLimit', 'globallyAttemptsLimit'], 'required'],
			[
				['leadId', 'typeId', 'priority', 'nextCallInterval', 'globallyAttemptsLimit', 'dailyAttemptsLimit'], 'integer',
			],
			['typeId', 'in', 'range' => array_keys(static::getTypesNames())],
			['priority', 'in', 'range' => array_keys(static::getPriorityNames())],
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

	public function getModel(): LeadDialer {
		if ($this->model === null) {
			$this->model = new LeadDialer();
		}
		return $this->model;
	}

	public function setModel(LeadDialer $model): void {
		$this->model = $model;
		$this->priority = $model->priority;
		$this->leadId = $model->lead_id;
		$this->typeId = $model->type_id;
		$config = $this->model->getConfig();
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->lead_id = $this->leadId;
		$model->type_id = $this->typeId;
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

	public static function getTypesNames(): array {
		return ArrayHelper::map(
			LeadDialerType::find()->asArray()->all(),
			'id',
			'name'
		);
	}

}
