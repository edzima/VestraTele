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
use yii\console\Controller;

class DemandForPayment extends Model {

	protected const LOG_CATEGORY = 'settlement.pay.demand';

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

	public ?Controller $consoleController = null;

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
			Yii::error([
				'message' => 'Mark Multiple Pays as Demand with Errors',
				'errors' => $this->getErrors(),
			], static::LOG_CATEGORY
			);
			return false;
		}
		$this->stdout("Mark multiple Pays. Which {$this->which}\n");
		if (empty($pays)) {
			$pays = $this->getPays();
		}
		$this->stdout('Pays to mark: ' . count($pays) . "\n");
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
		$this->stdout('Start Mark Pay: #' . $this->pay->getId() . "\n");
		if ($validate && !$this->validate()) {
			return false;
		}
		$messages = $this->pushMessages();
		if ($messages === null) {
			Yii::warning('Dont Push Messages for Pay: ' . $this->pay->getId(), static::LOG_CATEGORY);
			return false;
		}
		$this->stdout("Push Messages: $messages\n");
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

	public function createMessage(): IssuePayDelayedMessagesForm {
		return Yii::createObject($this->messageConfig);
	}

	/**
	 * @return IssuePay[]
	 * @throws InvalidConfigException
	 */
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

	protected function stdout(string $message): void {
		if ($this->consoleController) {
			$this->consoleController->stdout($message);
		}
	}

	private function getDelayedRange(): string {
		if (empty($this->delayedDays)) {
			return 'now';
		}
		return '- ' . $this->delayedDays . ' days';
	}

}
