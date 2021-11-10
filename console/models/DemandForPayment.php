<?php

namespace console\models;

use common\models\issue\IssuePay;
use common\models\issue\IssueUser;
use common\models\message\IssuePayDelayedMessagesForm;
use Yii;
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

	public ?int $delayedDays = null;
	public string $which = '';

	private array $pays = [];
	private IssuePay $pay;

	public function rules(): array {
		return [
			['which', 'required'],
			['which', 'string'],
			['which', 'in', 'range' => static::WHICH_AVAILABLE],
			['delayedDays', 'integer', 'min' => 0],
			['delayedDays', 'default', 'value' => null],
		];
	}

	public array $messageConfig = [
		'class' => IssuePayDelayedMessagesForm::class,
		'sendSmsToAgent' => true,
		'sendSmsToCustomer' => true,
		'workersTypes' => [IssueUser::TYPE_AGENT],
	];

	public function markAll(): ?int {
		if (!$this->validate()) {
			return false;
		}
		$count = 0;
		foreach ($this->getPays() as $pay) {
			if ($this->markOne($pay, false)) {
				$count++;
			}
		}
		return $count;
	}

	public function markOne(IssuePay $pay, bool $validate = true): bool {
		$this->pay = $pay;
		if ($validate && !$this->validate()) {
			return false;
		}
		if (empty($this->pushMessages())) {
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

	private function pushMessages(): int {
		$model = $this->createMessage();
		$model->setPay($this->pay);
		return $model->pushMessages();
	}

	private function createMessage(): IssuePayDelayedMessagesForm {
		return Yii::createObject($this->messageConfig);
	}

	public function getPays(): array {
		if (empty($this->pays)) {
			$this->pays = IssuePay::find()
				->onlyDelayed($this->delayedDays)
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

}
