<?php

namespace common\tests\unit\message;

use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSettlement;
use common\models\message\IssueSettlementCreateMessagesForm;
use common\models\message\IssueSmsForm;
use console\jobs\IssueSmsSendJob;
use Yii;
use yii\helpers\ArrayHelper;
use yii\mail\MessageInterface;

/**
 * @property IssueSettlementCreateMessagesForm $model
 */
class IssueSettlementCreateMessagesFormTest extends BaseIssueMessagesFormTest {

	protected const MODEL_CLASS = IssueSettlementCreateMessagesForm::class;
	protected const MESSAGE_TEMPLATE_FIXTURE_DIR = MessageTemplateFixtureHelper::DIR_ISSUE_SETTLEMENT_CREATE;

	private const DEFAULT_SETTLEMENT_TYPE = IssueSettlement::TYPE_HONORARIUM;
	private const DEFAULT_SETTLEMENT_VALUE = 1000;

	private ?IssueSettlement $settlement = null;

	public function testDefaultCustomerSms(): void {
		$this->giveIssue();
		$this->giveModel();

		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertSame('Sms About Create Honoarium Settlement in Issue(TYPE_1) for Customer.', $sms->note_title);
	}

	public function testSettlementLinkInEmails(): void {
		$this->giveIssue();
		$this->giveModel();

		$message = $this->model->getEmailToCustomer();
		$this->tester->assertStringNotContainsString($this->settlement->getFrontendUrl(), $message);

		$message = $this->model->getEmailToWorkers();
		$this->tester->assertMessageBodyContainsString($this->settlement->getFrontendUrl(), $message);
	}

	public function testSettlementValueInEmails(): void {
		$this->giveIssue();
		$this->giveModel();

		$message = $this->model->getEmailToCustomer();
		$value = Yii::$app->formatter->asCurrency($this->settlement->getValue());
		$this->tester->assertStringNotContainsString($value, $message);

		$message = $this->model->getEmailToWorkers();
		$this->tester->assertMessageBodyContainsString($value, $message);
	}

	public function testAdministrativeSettlement(): void {
		$this->giveIssue();
		$this->giveSettlement(['type' => IssueSettlement::TYPE_ADMINISTRATIVE]);
		$this->giveModel();

		$this->tester->assertNull($this->model->getSmsToCustomer());
		$this->tester->assertNull($this->model->getSmsToAgent());
		$this->tester->assertNull($this->model->getEmailToCustomer());
		$this->tester->assertNotNull($this->model->getEmailToWorkers());
	}

	public function testCustomerEmailForHonorarium(): void {

		$this->giveModel();
		$model = $this->model;
		$this->tester->assertTrue($model->pushCustomerMessages());
		$this->tester->seeEmailIsSent();
		/** @var MessageInterface $email */
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertArrayHasKey($this->settlement->getIssueModel()->customer->email, $email->getTo());
		$this->tester->assertSame(
			'Create Settlement Honorarium for Customer.',
			$email->getSubject()
		);
	}

	public function testSmsAboutCreateToCustomerForHonorarium(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('many-pays-without-costs');
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$model->scenario = CalculationForm::SCENARIO_CREATE;
		$this->tester->assertTrue($model->sendSmsAboutCreateToCustomer());
		/**
		 * @var IssueSmsSendJob $job
		 */
		$job = $this->tester->grabLastPushedJob();
		$this->tester->assertNotEmpty($job);
		$this->tester->assertInstanceOf(IssueSmsSendJob::class, $job);
		$this->tester->assertSame($settlement->getIssueId(), $job->issue_id);
		$this->tester->assertSame($model->getOwner(), $job->owner_id);
		$this->tester->assertSame(IssueSmsForm::normalizePhone($settlement->getIssueModel()->customer->profile->phone), $job->message->getDst());
		$this->tester->assertSame('New Settlement in Your Issue. Your Agent: Nowak Peter - +48 122 222 300', $job->message->getMessage());
		$this->tester->assertSame('Sms About Create Settlement Honorarium for Customer.', $job->note_title);

		$smsId = $job->run();
		$this->tester->assertNotEmpty($smsId);
		$this->tester->seeRecord(IssueNote::class, [
			'issue_id' => $settlement->getIssueId(),
			'user_id' => $model->getOwner(),
			'description' => $job->message->getMessage(),
			'type' => IssueNote::genereateSmsType($job->message->getDst(), $smsId),
		]);
	}

	public function testSmsAboutCreateToAgentForHonorarium(): void {
		$this->giveSettlement();
		$this->giveModel();
		$sms = $this->model->getSmsToAgent();
		$this->tester->assertNotNull($sms);
		$this->tester->assertSame($sms->phone, $this->settlement->getIssueModel()->agent->getPhone());
		/**
		 * @var IssueSmsSendJob $job
		 */
		$job = $this->tester->grabLastPushedJob();
		$this->tester->assertNotEmpty($job);
		$this->tester->assertInstanceOf(IssueSmsSendJob::class, $job);
		$this->tester->assertSame($settlement->getIssueId(), $job->issue_id);
		$this->tester->assertSame($model->getOwner(), $job->owner_id);
		$this->tester->assertSame(IssueSmsForm::normalizePhone($settlement->getIssueModel()->agent->profile->phone), $job->message->getDst());
		$this->tester->assertSame('New Settlement in Your Issue. Customer: Wayne John - +48 673 222 110', $job->message->getMessage());
		$this->tester->assertSame('Sms About Create Settlement Honorarium for Agent.', $job->note_title);
	}

	public function testWorkersEmails(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('many-pays-without-costs');
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$this->tester->assertTrue($model->sendEmailAboutCreateToWorker());
		$this->tester->seeEmailIsSent();
		/** @var MessageInterface $email */
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertArrayHasKey($settlement->getIssueModel()->agent->email, $email->getTo());
		$this->tester->assertArrayHasKey($settlement->getIssueModel()->tele->email, $email->getTo());
		$this->tester->assertSame(
			'Create Settlement Honorarium for Worker.',
			$email->getSubject()
		);
	}

	protected function giveModel(IssueInterface $issue = null, array $config = []): void {
		$settlementConfig = ArrayHelper::remove($config, 'settlementConfig', []);
		parent::giveModel($issue, $config);
		if ($this->settlement === null || !empty($settlementConfig)) {
			$this->giveSettlement($settlementConfig);
		}
		$this->model->setSettlement($this->settlement);
	}

	private function giveSettlement(array $config = []) {
		if (!isset($config['type'])) {
			$config['type'] = static::DEFAULT_SETTLEMENT_TYPE;
		}
		if (!isset($config['value'])) {
			$config['value'] = static::DEFAULT_SETTLEMENT_VALUE;
		}
		$config['issue_id'] = $this->issue->getIssueId();
		$config['id'] = 1000;
		$this->settlement = new IssuePayCalculation($config);
	}
}
