<?php

namespace common\tests\unit\message;

use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssueInterface;
use common\models\issue\IssuePayInterface;
use common\models\issue\IssueSettlement;
use common\models\issue\IssueUser;
use common\models\message\IssuePayPayedMessagesForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\swiftmailer\Message;
use ymaker\email\templates\entities\EmailTemplate;

/**
 * @property IssuePayPayedMessagesForm $model
 */
class IssuePayPayedMessagesFormTest extends BaseIssueMessagesFormTest {

	protected const MODEL_CLASS = IssuePayPayedMessagesForm::class;
	protected const MESSAGE_TEMPLATE_FIXTURE_DIR = MessageTemplateFixtureHelper::DIR_ISSUE_PAY_PAYED;
	protected const DEFAULT_PAY_VALUE = 1023;

	private SettlementFixtureHelper $settlementFixtureHelper;

	private ?IssuePayInterface $pay = null;

	public function _before() {
		parent::_before();
		$this->settlementFixtureHelper = new SettlementFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			parent::_fixtures(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::owner(),
			SettlementFixtureHelper::pay()
		);
	}

	public function testPayValue(): void {
		$this->giveModel();
		$email = $this->model->getEmailToCustomer();
		$this->tester->assertNotNull($email);
		$this->tester->assertMessageBodyContainsString($this->getFormattedPayValue(), $email);
		$email = $this->model->getEmailToWorkers();
		$this->tester->assertNotNull($email);
		$this->tester->assertMessageBodyContainsString($this->getFormattedPayValue(), $email);
	}

	public function testPartPaymentCustomerSms(): void {
		$this->giveModel();
		$this->model->isPartPayment = true;
		$smsPart = $this->model->getSmsToCustomer();
		$this->tester->assertNotNull($smsPart);

		$this->model->isPartPayment = false;
		$smsNotPart = $this->model->getSmsToCustomer();
		$this->tester->assertNotSame($smsPart->message, $smsNotPart->message);
	}

	/**
	 * @dataProvider keysProvider
	 * @param string $generated
	 * @param string $expected
	 */
	public function testKeys(string $generated, string $expected): void {
		$this->tester->assertSame($expected, $generated);
	}

	public function keysProvider(): array {
		return [
			'Customer Email for Honorarium With Issue Types as List' => [
				IssuePayPayedMessagesForm::generateKey(
					IssuePayPayedMessagesForm::TYPE_EMAIL,
					IssuePayPayedMessagesForm::keyCustomer(),
					[1, 2],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'email.issue.settlement.pay.payed.customer.settlementType:30.issueTypes:1,2',
			],
			'Customer SMS for Part Payed Honorarium With Issue Types as List' => [
				IssuePayPayedMessagesForm::generateKey(
					IssuePayPayedMessagesForm::TYPE_SMS,
					IssuePayPayedMessagesForm::keyCustomer([IssuePayPayedMessagesForm::KEY_PART_PAYMENT]),
					[1, 2],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'sms.issue.settlement.pay.payed.customer.part-payment.settlementType:30.issueTypes:1,2',
			],
			'Customer Email for Honorarium Without Issue Types' => [
				IssuePayPayedMessagesForm::generateKey(
					IssuePayPayedMessagesForm::TYPE_EMAIL,
					IssuePayPayedMessagesForm::keyCustomer(),
					[],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'email.issue.settlement.pay.payed.customer.settlementType:30',
			],
			'Workers Email for Honorarium With Issue Types as List' => [
				IssuePayPayedMessagesForm::generateKey(
					IssuePayPayedMessagesForm::TYPE_EMAIL,
					IssuePayPayedMessagesForm::keyWorkers(),
					[1, 2],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'email.issue.settlement.pay.payed.workers.settlementType:30.issueTypes:1,2',
			],
			'Workers Email for Honorarium Without Issue Types' => [
				IssuePayPayedMessagesForm::generateKey(
					IssuePayPayedMessagesForm::TYPE_EMAIL,
					IssuePayPayedMessagesForm::keyWorkers(),
					[],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'email.issue.settlement.pay.payed.workers.settlementType:30',
			],
		];
	}

	public function testEmailToCustomer(): void {
		codecept_debug(EmailTemplate::find()->select('key')->column());
		$this->giveModel();
		$this->model->sendSmsToCustomer = false;
		$this->tester->assertTrue((bool) $this->model->pushCustomerMessages());
		$this->tester->seeEmailIsSent();
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertTrue(array_key_exists($this->pay->calculation->getIssueModel()->customer->email, $email->getTo()));
		$this->tester->assertSame('Email. About Payed Pay Issue(TYPE_1 and TYPE_2) for Customer.', $email->getSubject());

		$this->tester->assertMessageBodyContainsString(
			$this->pay->calculation->getFrontendUrl(),
			$email
		);
	}

	public function testEmailToWorkers(): void {
		$this->giveModel();
		$model = $this->model;
		$this->model->sendSmsToAgent = false;

		$model->workersTypes = [IssueUser::TYPE_AGENT, IssueUser::TYPE_TELEMARKETER];
		$this->tester->assertTrue((bool) $this->model->pushWorkersMessages());
		$this->tester->seeEmailIsSent();
		$email = $this->tester->grabLastSentEmail();
		/**
		 * @var Message $email
		 */
		$this->tester->assertTrue(array_key_exists($this->pay->calculation->getIssueModel()->agent->email, $email->getTo()));
		$this->tester->assertTrue(array_key_exists($this->pay->calculation->getIssueModel()->tele->email, $email->getTo()));
		$this->tester->assertSame('Email. Pay Payed: ' . $this->getFormattedPayValue() . ' 1/10/2021 (All types) for Worker.', $email->getSubject());
		$this->tester->assertMessageBodyContainsString(
			$this->pay->calculation->getFrontendUrl(),
			$email
		);
	}

	private function getFormattedPayValue(): string {
		return Yii::$app->formatter->asCurrency($this->pay->getValue());
	}

	protected function giveModel(IssueInterface $issue = null, array $config = []): void {
		$payConfig = ArrayHelper::remove($config, 'payConfig', []);
		if ($this->pay === null || !empty($payConfig)) {
			$this->givePay($payConfig);
		}
		if ($issue === null) {
			$issue = $this->pay->calculation;
			$this->issue = $issue;
		}
		parent::giveModel($issue, $config);
		$this->model->setPay($this->pay);
	}

	protected function givePay(array $config = []): void {
		$value = ArrayHelper::getValue($config, 'value', static::DEFAULT_PAY_VALUE);
		$this->pay = $this->settlementFixtureHelper->findPay(
			$this->settlementFixtureHelper->havePay($value, $config)
		);
	}

}
