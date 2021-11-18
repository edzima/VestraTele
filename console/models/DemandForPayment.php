<?php

namespace console\models;

use common\models\issue\IssuePay;
use common\models\issue\IssueUser;
use common\models\message\IssuePayDelayedMessagesForm;
use common\models\settlement\PayInterface;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Model;

class DemandForPayment extends Model {

	public const WHICH_FIRST = 'first';
	public const WHICH_SECOND = 'second';
	public const WHICH_THIRD = 'third';

	protected const WHICH_AVAILABLE = [
		self::WHICH_FIRST,
		self::WHICH_SECOND,
		self::WHICH_THIRD,
	];

	public ?int $smsOwnerId = null;
	public ?int $delayedDays = null;
	public string $which = '';

	public array $messageConfig = [
		'class' => IssuePayDelayedMessagesForm::class,
		'sendSmsToAgent' => true,
		'sendSmsToCustomer' => true,
		'workersTypes' => [IssueUser::TYPE_AGENT],
	];

	private array $pays = [];
	private IssuePay $pay;

	public function rules(): array {
		return [
			[['which', '!smsOwnerId'], 'required'],
			['which', 'string'],
			['which', 'in', 'range' => static::WHICH_AVAILABLE],
			['delayedDays', 'integer', 'min' => 0],
			['delayedDays', 'default', 'value' => null],
		];
	}

	public function markMultiple(array $pays = []): ?int {
		if (!$this->validate()) {
			return false;
		}
		if (empty($pays)) {
			$pays = $this->getPays();
		}
		$count = 0;
		foreach ($pays as $pay) {
			$this->setPay($pay);
			if ($this->markOne(false)) {
				$count++;
			}
		}
		return $count;
	}

	public function setPay(PayInterface $pay): void {
		if ($pay->isPayed()) {
			throw new InvalidArgumentException('Pay cannot be payed.');
		}
		if ($pay->getDeadlineAt() === null) {
			throw new InvalidArgumentException('Pay must have deadline.');
		}
		if (!$pay->isDelayed($this->getDelayedRange())) {
			throw new InvalidArgumentException('Pay must be delayed.');
		}
		if ($pay->getStatus() === $this->getPayStatus()) {
			throw new InvalidArgumentException('Pay cannot have same demand status.');
		}

		$this->pay = $pay;
	}

	public function markOne(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		if ($this->pushMessages() === null) {
			return false;
		}
		$this->updateStatus();
		return true;
	}

	private function updateStatus(): bool {
		$pay = $this->pay;
		$status = $this->getPayStatus();
		if ($pay->status === $status) {
			return false;
		}
		$pay->status = $status;
		return $pay->update(false, ['status']);
	}

	private function pushMessages(): ?int {
		$model = $this->createMessage();
		$model->sms_owner_id = $this->smsOwnerId;
		$model->setPay($this->pay);
		$model->whichDemand = $this->which;
		return $model->pushMessages();
	}

	private function createMessage(): IssuePayDelayedMessagesForm {
		return Yii::createObject($this->messageConfig);
	}

	public function getPays(): array {
		if (empty($this->pays)) {
			$this->pays = IssuePay::find()
				->onlyDelayed($this->delayedDays)
				->andWhere('status IS NULL OR status != :status', ['status' => $this->getPayStatus()])
				->all();
		}
		return $this->pays;
	}

	public function getPayStatus(): int {
		switch ($this->which) {
			case static::WHICH_FIRST:
				return IssuePay::STATUS_DEMAND_FOR_PAYMENT_FIRST;
			case static::WHICH_SECOND:
				return IssuePay::STATUS_DEMAND_FOR_PAYMENT_SECOND;
			case static::WHICH_THIRD:
				return IssuePay::STATUS_DEMAND_FOR_PAYMENT_THIRD;
		}
		throw new InvalidConfigException('Invalid Which.');
	}

	private function getDelayedRange(): string {
		if (empty($this->delayedDays)) {
			return 'now';
		}
		return '- ' . $this->delayedDays . ' days';
	}

}
