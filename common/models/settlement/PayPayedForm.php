<?php

namespace common\models\settlement;

use common\helpers\MessageTemplateKeyHelper;
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
	public bool $sendEmailToCustomer = true;
	public bool $sendEmailToWorkers = true;

	private IssuePayInterface $pay;

	public function __construct(IssuePayInterface $pay, $config = []) {
		if ($pay->isPayed()) {
			throw new InvalidConfigException('$pay can not be payed.');
		}
		$this->pay = $pay;
		parent::__construct($config);
	}

	public function init(): void {
		parent::init();
		$this->sendEmailToCustomer = !empty($this->pay->calculation->getIssueModel()->customer->email);
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
			'sendEmailToCustomer' => Yii::t('settlement', 'Send Email to Customer'),
			'sendEmailToWorkers' => Yii::t('settlement', 'Send Email to Workers'),
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
		if (!$this->pay->isPayed() || empty($this->pay->calculation->getIssueModel()->customer->email)) {
			return false;
		}
		$issue = $this->pay->calculation->getIssueModel();
		$template = Yii::$app->messageTemplate->getIssueTypeTemplatesLikeKey(
			MessageTemplateKeyHelper::generateKey(
				[
					MessageTemplateKeyHelper::SETTLEMENT_PAY_PAID,
					MessageTemplateKeyHelper::CUSTOMER,
				]
			), $issue->type_id
		);
		if ($template === null) {
			return false;
		}
		$template->parseBody([
			'agentFullName' => $issue->agent->getFullName(),
			'agentEmail' => $issue->agent->email,
			'agentPhone' => $issue->agent->profile->phone,
		]);
		return Yii::$app
			->mailer
			->compose()
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' ' . Yii::t('settlement', 'Settlements')])
			->setTo($this->pay->calculation->getIssueModel()->customer->email)
			->setSubject($template->getSubject())
			->setHtmlBody($template->getBody())
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

		$issue = $this->pay->calculation->getIssueModel();
		$template = Yii::$app->messageTemplate->getIssueTypeTemplatesLikeKey(
			MessageTemplateKeyHelper::generateKey(
				[
					MessageTemplateKeyHelper::SETTLEMENT_PAY_PAID,
					MessageTemplateKeyHelper::WORKER,
				]
			), $issue->type_id
		);
		if ($template === null) {
			return false;
		}

		$template->parseBody([
			'issue' => $issue->getIssueName(),
			'customer' => $issue->customer->getFullName(),
			'link' => $this->pay->calculation->getFrontendUrl(),
			'payValue' => Yii::$app->formatter->asCurrency($this->pay->getValue()),
		]);

		return Yii::$app
			->mailer
			->compose()
			->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' ' . Yii::t('settlement', 'Settlements')])
			->setTo($emails)
			->setSubject($template->getSubject())
			->setHtmlBody($template->getBody())
			->send();
	}
}
