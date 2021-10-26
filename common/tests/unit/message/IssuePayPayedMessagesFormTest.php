<?php

namespace common\tests\unit\message;

use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssueInterface;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayInterface;
use common\models\issue\IssueUser;
use common\models\message\IssuePayPayedMessagesForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\swiftmailer\Message;

/**
 * @property IssuePayPayedMessagesForm $model
 */
class IssuePayPayedMessagesFormTest extends BaseIssueMessagesFormTest {

	protected const MODEL_CLASS = IssuePayPayedMessagesForm::class;
	protected const MESSAGE_TEMPLATE_FIXTURE_DIR = MessageTemplateFixtureHelper::DIR_ISSUE_PAY_PAYED;

	private ?IssuePayInterface $pay = null;

	public function _fixtures(): array {
		return array_merge(
			parent::_fixtures(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay()
		);
	}

	public function testPayValue(): void {
		$this->givePay(['calculation_id' => 1]);
		$this->giveModel();
		$email = $this->model->getEmailToCustomer();
		$this->tester->assertNotNull($email);
		$this->tester->assertMessageBodyContainsString(Yii::$app->formatter->asCurrency($this->pay->getValue()), $email);

		$email = $this->model->getEmailToWorkers();
		$this->tester->assertNotNull($email);
		$this->tester->assertMessageBodyContainsString(Yii::$app->formatter->asCurrency($this->pay->getValue()), $email);
	}

	public function testEmailToCustomer(): void {
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
		$this->pay = $this->tester->grabRecord(IssuePay::class, $config);
	}
}
