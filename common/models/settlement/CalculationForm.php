<?php

namespace common\models\settlement;

use common\models\issue\Issue;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use Yii;
use yii\base\InvalidConfigException;

class CalculationForm extends PayForm {

	public ?int $issue_id = null;
	protected ?int $owner = null;
	public $type;

	public ?int $providerType = null;

	private ?IssuePayCalculation $model = null;

	/**
	 * @return int
	 * @throws InvalidConfigException
	 */
	protected function getOwner(): int {
		if ($this->owner === null) {
			throw new InvalidConfigException('Owner must be set as integer.');
		}
		return $this->owner;
	}

	public function setOwner(int $id): void {
		$this->owner = $id;
	}

	public function rules(): array {
		return array_merge([
			[['issue_id', 'type', 'providerType',], 'required'],
			[['issue_id', 'owner', 'type', 'providerType'], 'integer'],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			['providerType', 'in', 'range' => array_keys(IssuePayCalculation::getProvidersTypesNames())],
			[['owner'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner' => 'id']],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
		], parent::rules());
	}

	public function attributeLabels(): array {
		return array_merge([
			'providerType' => Yii::t('settlement', 'Provider'),
		], parent::attributeLabels());
	}

	public function setCalculation(IssuePayCalculation $model): void {
		$this->model = $model;
		$this->issue_id = $model->issue_id;
		$this->owner = $model->owner_id;
		if ($model->getPaysCount() === 1) {
			$this->setPay($model->getPays()->one());
		}
		$this->value = $model->getValue()->toFixed(2);
	}

	public function getModel(): IssuePayCalculation {
		if ($this->model === null) {
			$model = new IssuePayCalculation();
			$model->issue_id = $this->issue_id;
			$model->owner_id = $this->owner;
			$this->model = $model;
		}
		return $this->model;
	}

	public function getIssue(): Issue {
		return $this->getModel()->issue;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		if ($model->isNewRecord) {
			$model->stage_id = $this->getIssue()->stage_id;
		}
		$model->owner_id = $this->getOwner();
		$model->payment_at = $this->getPaymentAt() ? $this->getPaymentAt()->format($this->dateFormat) : null;
		$model->issue_id = $this->issue_id;
		$model->value = $this->getValue()->toFixed(2);
		$model->type = $this->type;
		$model->provider_type = $this->providerType;
		$model->provider_id = $this->getProviderId();
		if (!$model->save(false)) {
			return false;
		}
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
		return true;
	}

	/**
	 * @return int
	 * @throws InvalidConfigException
	 */
	public function getProviderId(): int {
		switch ($this->providerType) {
			case IssuePayCalculation::PROVIDER_CLIENT:
				return $this->getModel()->issue->customer->id;
			case IssuePayCalculation::PROVIDER_RESPONSIBLE_ENTITY:
				return $this->getModel()->issue->entity_responsible_id;
			default:
				throw new InvalidConfigException('Invalid provider type.');
		}
	}

	public function getProvidersNames(): array {
		return $this->getModel()->getProvidersNames();
	}

	public static function getTypesNames(): array {
		return IssuePayCalculation::getTypesNames();
	}

}
