<?php

namespace common\models\settlement;

use common\models\issue\IssuePayInterface;
use common\models\issue\IssueUser;
use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

class PayPayedForm extends Model {

	public ?int $id = null;
	public ?string $date = null;
	public string $transfer_type = TransferType::TRANSFER_TYPE_BANK;

	private IssuePayInterface $pay;

	public function __construct(IssuePayInterface $pay, $config = []) {
		if ($pay->isPayed()) {
			throw new InvalidConfigException('$pay can not be payed.');
		}
		$this->pay = $pay;
		parent::__construct($config);
	}

	public function rules(): array {
		return [
			[['transfer_type', 'date'], 'required'],
			['transfer_type', 'string'],
			['date', 'date', 'format' => 'Y-m-d'],
			['transfer_type', 'in', 'range' => array_keys($this->getPay()::getTransfersTypesNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'transfer_type' => Yii::t('settlement', 'Transfer Type'),
			'date' => Yii::t('settlement', 'Pay at'),
		];
	}

	public function getPay(): IssuePayInterface {
		return $this->pay;
	}

	public function pay(): bool {
		if ($this->validate()) {
			$pay = $this->getPay();
			/** @noinspection PhpUnhandledExceptionInspection */
			return $pay->markAsPaid(new DateTime($this->date), $this->transfer_type);
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function sendEmailToCustomer(): bool {
		if (!$this->pay->isPayed()) {
			return false;
		}
		return Yii::$app
			->mailer
			->compose(
				['html' => 'settlements/paidPay-customer-html', 'text' => 'settlements/paidPay-customer-text'],
				[
					'pay' => $this->pay,
					'customer' => $this->pay->calculation->getIssueModel()->customer,
				]
			)
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' ' . Yii::t('settlement', 'Settlements')])
			->setTo($this->pay->calculation->getIssueModel()->customer->email)
			->setSubject(Yii::t('settlement', 'Mark Pay: {value} as Paid.', ['value' => Yii::$app->formatter->asCurrency($this->pay->getValue())]))
			->send();
	}

	/**
	 * @param array $types
	 * @return int
	 */
	public function sendEmailsToWorkers(array $types = [IssueUser::TYPE_AGENT, IssueUser::TYPE_TELEMARKETER]): bool {
		if (!empty($types) && !$this->getPay()->isPayed()) {
			return false;
		}
		$emails = $this->getPay()->calculation->getIssueModel()->getUsers()
			->withTypes($types)
			->select('user.email')
			->joinWith('user')
			->column();
		if (empty($emails)) {
			return false;
		}

		return Yii::$app
			->mailer
			->compose(
				['html' => 'settlements/paidPay-worker-html', 'text' => 'settlements/paidPay-worker-text'],
				[
					'pay' => $this->pay,
				]
			)
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' ' . Yii::t('settlement', 'Settlements')])
			->setTo($emails)
			->setSubject(Yii::t('settlement', 'Mark Pay: {value} as Paid.', ['value' => Yii::$app->formatter->asCurrency($this->pay->getValue())]))
			->send();
	}
}
