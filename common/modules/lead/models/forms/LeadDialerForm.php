<?php

namespace common\modules\lead\models\forms;

use common\helpers\ArrayHelper;
use common\modules\lead\entities\Dialer;
use common\modules\lead\entities\DialerConfig;
use common\modules\lead\entities\DialerConfigInterface;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\LeadDialerType;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class LeadDialerForm extends Model implements DialerConfigInterface {

	public const SCENARIO_MULTIPLE = 'multiple';

	public $leadId;
	public $typeId;
	public $priority;
	public $status = Dialer::STATUS_NEW;
	public $destination;

	public $dailyAttemptsLimit;
	public $globallyAttemptsLimit;
	public $nextCallInterval;

	private ?LeadDialer $model = null;

	public function init() {
		parent::init();
		if (empty($this->dailyAttemptsLimit) && empty($this->globallyAttemptsLimit) && empty($this->nextCallInterval)) {
			$this->setConfig(new DialerConfig());
		}
	}

	public function rules(): array {
		return [
			[
				[
					'leadId', 'typeId', 'priority', 'status',
					'nextCallInterval', 'dailyAttemptsLimit', 'globallyAttemptsLimit',
				], 'required',
			],
			[
				[
					'typeId', 'priority', 'status',
					'nextCallInterval', 'globallyAttemptsLimit', 'dailyAttemptsLimit',
				], 'integer',
			],
			['destination', 'integer', 'min' => 0],
			['destination', 'default', 'value' => null],
			['leadId', 'integer', 'on' => static::SCENARIO_DEFAULT],
			['typeId', 'in', 'range' => array_keys(static::getTypesNames())],
			['priority', 'in', 'range' => array_keys(static::getPriorityNames())],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
			[
				'leadId', 'exist', 'targetClass' => Lead::class,
				'targetAttribute' => ['leadId' => 'id'],
				'allowArray' => false,
				'except' => static::SCENARIO_MULTIPLE,
			],
			[
				'leadId', 'exist', 'targetClass' => Lead::class,
				'targetAttribute' => 'id',
				'on' => static::SCENARIO_MULTIPLE,
				'allowArray' => true,
			],
			[
				['typeId'], 'unique',
				'targetAttribute' => ['leadId' => 'lead_id', 'typeId' => 'type_id'],
				'targetClass' => LeadDialer::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},
				'except' => static::SCENARIO_MULTIPLE,
			],
			[
				'leadId', 'filter',
				'on' => static::SCENARIO_MULTIPLE,
				'filter' => function (array $ids): array {
					$typeIds = LeadDialer::find()
						->select('lead_id')
						->andWhere(['type_id' => $this->typeId])
						->indexBy('lead_id')
						->column();

					return array_filter($ids, static function (int $id) use ($typeIds) {
						return !isset($typeIds[$id]);
					});
				},
			],
			[
				'leadId', 'filter',
				'on' => static::SCENARIO_MULTIPLE,
				'filter' => function (array $ids): array {
					$typeIds = LeadDialer::find()
						->select('lead_id')
						->andWhere(['type_id' => $this->typeId])
						->indexBy('lead_id')
						->column();

					return array_filter($ids, static function (int $id) use ($typeIds) {
						return !isset($typeIds[$id]);
					});
				},
			],
		];
	}

	public function attributeLabels(): array {
		return [
			'leadId' => Yii::t('lead', 'Lead'),
			'typeId' => Yii::t('lead', 'Type'),
			'priority' => Yii::t('lead', 'Priority'),
			'status' => Yii::t('lead', 'Status'),
			'dailyAttemptsLimit' => Yii::t('lead', 'Daily attempts limit'),
			'globallyAttemptsLimit' => Yii::t('lead', 'Globally attempts limit'),
			'nextCallInterval' => Yii::t('lead', 'Next call interval'),
			'destination' => Yii::t('lead', 'Destination'),
		];
	}

	public function setModel(LeadDialer $model): void {
		$this->model = $model;
		$this->priority = $model->priority;
		$this->status = $model->status;
		$this->leadId = $model->lead_id;
		$this->typeId = $model->type_id;
		$this->destination = $model->destination;
		$this->setConfig($model->getConfig());
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
		$model->destination = $this->destination;
		$model->setConfig($this);
		return $model->save(false);
	}

	public function saveMultiple(): ?int {
		if ($this->scenario !== static::SCENARIO_MULTIPLE || !$this->validate()) {
			return null;
		}
		$ids = (array) $this->leadId;
		$config = LeadDialer::generateDialerConfigColumn($this);
		$rows = [];
		foreach ($ids as $id) {
			$rows[] = [
				'lead_id' => $id,
				'type_id' => $this->typeId,
				'status' => $this->status,
				'priority' => $this->priority,
				'destination' => $this->destination,
				'dialer_config' => $config,
				'created_at' => date(DATE_ATOM),
				'updated_at' => date(DATE_ATOM),
			];
		}
		return LeadDialer::getDb()->createCommand()
			->batchInsert(LeadDialer::tableName(), [
				'lead_id',
				'type_id',
				'status',
				'priority',
				'destination',
				'dialer_config',
				'created_at',
				'updated_at',
			], $rows)
			->execute();
	}

	public function getModel(): LeadDialer {
		if ($this->model === null) {
			$this->model = new LeadDialer();
		}
		return $this->model;
	}

	public function setConfig(DialerConfigInterface $config): void {
		$this->dailyAttemptsLimit = $config->getDailyAttemptsLimit();
		$this->globallyAttemptsLimit = $config->getGloballyAttemptsLimit();
		$this->nextCallInterval = $config->getNextCallInterval();
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
