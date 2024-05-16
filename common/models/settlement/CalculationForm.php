<?php

namespace common\models\settlement;

use common\components\provision\exception\Exception;
use common\models\entityResponsible\EntityResponsible;
use common\models\issue\Issue;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class CalculationForm extends PayForm {

	public const PROVIDER_TYPE_RESPONSIBLE_ENTITY = IssuePayCalculation::PROVIDER_RESPONSIBLE_ENTITY;
	public const PROVIDER_TYPE_CLIENT = IssuePayCalculation::PROVIDER_CLIENT;

	private Issue $issue;
	private int $owner;
	public $type;

	public ?int $providerType = null;
	public $costs_ids = [];

	public $entityProviderId;

	private ?IssuePayCalculation $model = null;

	public function __construct(int $owner_id, Issue $issue, $config = []) {
		$this->issue = $issue;
		$this->entityProviderId = $issue->entity_responsible_id;
		$this->owner = $owner_id;
		parent::__construct($config);
	}

	public static function createFromModel(IssuePayCalculation $model): self {
		$self = new static(
			$model->owner_id,
			$model->issue
		);
		$self->setCalculation($model);
		return $self;
	}

	public function rules(): array {
		return array_merge([
			[['type', 'providerType',], 'required'],
			[['owner', 'type', 'providerType', 'entityProviderId'], 'integer'],
			[
				'entityProviderId', 'required', 'when' => function () {
				return $this->providerType === static::PROVIDER_TYPE_RESPONSIBLE_ENTITY;
			}, 'enableClientValidation' => false,
			],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			['costs_ids', 'in', 'range' => array_keys($this->getCostsData()), 'allowArray' => true],
			['providerType', 'in', 'range' => array_keys(IssuePayCalculation::getProvidersTypesNames())],
			['entityProviderId', 'in', 'range' => array_keys(static::getEntityResponsibleNames())],
		], parent::rules());
	}

	public static function getEntityResponsibleNames(): array {
		return ArrayHelper::map(
			EntityResponsible::find()->asArray()->all(),
			'id',
			'name'
		);
	}

	public function attributeLabels(): array {
		return array_merge([
			'providerType' => Yii::t('settlement', 'Provider'),
			'type' => Yii::t('settlement', 'Type'),
			'costs_ids' => Yii::t('settlement', 'Costs'),
			'entityProviderId' => Yii::t('settlement', 'Entity responsible'),
		], parent::attributeLabels());
	}

	public function isRequiredPaymentAt(): bool {
		return false;
	}

	public function isRequiredDeadlineAt(): bool {
		return false;
	}

	public function setCalculation(IssuePayCalculation $model): void {
		$this->model = $model;
		$this->issue = $model->issue;
		$this->owner = $model->owner_id;
		$this->costs_ids = ArrayHelper::getColumn($model->costs, 'id');
		$this->providerType = $model->provider_type;
		if ($model->provider_type === static::PROVIDER_TYPE_RESPONSIBLE_ENTITY) {
			$this->entityProviderId = $model->provider_id;
		}
		$this->type = $model->type;
		if ($model->getPaysCount() === 1) {
			$this->setPay($model->getPays()->one());
		}
		$this->value = $model->getValue()->toFixed(2);
	}

	public function getCostsData(): array {
		return ArrayHelper::map($this->getIssue()->costs, 'id', 'typeNameWithValue');
	}

	public function getIssue(): Issue {
		return $this->issue;
	}

	public function getModel(): IssuePayCalculation {
		if ($this->model === null) {
			$model = new IssuePayCalculation();
			$model->issue_id = $this->getIssue()->id;
			$model->owner_id = $this->owner;
			$this->model = $model;
		}
		return $this->model;
	}

	public function getOwner(): int {
		return $this->owner;
	}

	public function setOwner(int $id): void {
		$this->owner = $id;
	}

	public function save(bool $throwException = false): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->owner_id = $this->owner;
		$model->issue_id = $this->getIssue()->id;
		$isNewRecord = $model->isNewRecord;
		if ($isNewRecord) {
			$model->stage_id = $this->getIssue()->stage_id;
		}

		$model->payment_at = $this->getPaymentAt() ? $this->getPaymentAt()->format($this->dateFormat) : null;
		$model->value = $this->getValue()->toFixed(2);
		$model->type = $this->type;
		$model->provider_type = $this->providerType;
		$model->provider_id = $this->getProviderId();
		if (!$model->save(false)) {
			return false;
		}
		$this->saveCosts();
		$paysCount = $model->getPaysCount();
		if ($paysCount === 0) {
			$calculationPay = new IssuePay();
			$calculationPay->setPay($this->generatePay(false));
			$model->link('pays', $calculationPay);
		}
		if ($paysCount === 1) {
			$calculationPay = $model->getPays()->one();
			$calculationPay->setPay($this->generatePay(false));
			$calculationPay->save(false);
		}
		if (!$isNewRecord) {
			Yii::$app->provisions->removeForPays($model->getPays()->getIds(true));
		}
		try {
			Yii::$app->provisions->settlement($model);
		} catch (Exception $exception) {
			if ($throwException) {
				throw $exception;
			}
		}
		return true;
	}

	protected function saveCosts(): void {
		$model = $this->getModel();
		if ($model->getCosts()->count() > 0) {
			$model->unlinkCosts();
		}

		if (!empty($this->costs_ids)) {
			$model->linkCosts((array) $this->costs_ids);
		}
	}

	/**
	 * @return int
	 * @throws InvalidConfigException
	 */
	public function getProviderId(): int {
		switch ($this->providerType) {
			case static::PROVIDER_TYPE_CLIENT:
				return $this->getModel()->issue->customer->id;
			case static::PROVIDER_TYPE_RESPONSIBLE_ENTITY:
				return $this->entityProviderId ?: $this->getModel()->issue->entity_responsible_id;
			default:
				throw new InvalidConfigException('Invalid provider type.');
		}
	}

	public function getProvidersNames(): array {
		return [
			static::PROVIDER_TYPE_CLIENT => Yii::t('settlement', 'Customer'),
			static::PROVIDER_TYPE_RESPONSIBLE_ENTITY => Yii::t('settlement', 'Entity responsible'),
		];
	}

	public static function getTypesNames(): array {
		return IssuePayCalculation::getTypesNames();
	}

}
