<?php

namespace backend\tests\unit\settlement;

use backend\modules\issue\models\IssueSmsForm;
use backend\modules\settlement\models\CalculationForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssueNote;
use console\jobs\IssueSmsSendJob;
use yii\mail\MessageInterface;

class CalculationFormTest extends Unit {

	private SettlementFixtureHelper $settlementFixtureHelper;

	public function _before(): void {
		$this->settlementFixtureHelper = new SettlementFixtureHelper($this->tester);
		parent::_before();
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(true),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::owner(),
			MessageTemplateFixtureHelper::fixture()
		);
	}

	public function testCreateEmailToCustomerWithoutTemplate(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('not-payed-with-double-costs');
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$model->scenario = CalculationForm::SCENARIO_CREATE;
		$this->tester->assertFalse($model->sendEmailAboutCreateToCustomer());
	}

	public function testCreateEmailToWorkersWithoutTemplate(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('not-payed-with-double-costs');
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$model->scenario = CalculationForm::SCENARIO_CREATE;
		$this->tester->assertFalse($model->sendEmailAboutCreateToWorker());
	}

	public function testCustomerEmailForHonorarium(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('many-pays-without-costs');
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$model->scenario = CalculationForm::SCENARIO_CREATE;

		$this->tester->assertTrue($model->sendEmailAboutCreateToCustomer());
		$this->tester->seeEmailIsSent();
		/** @var MessageInterface $email */
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertArrayHasKey($settlement->getIssueModel()->customer->email, $email->getTo());
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
		$settlement = $this->settlementFixtureHelper->grabSettlement('many-pays-without-costs');
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$model->scenario = CalculationForm::SCENARIO_CREATE;
		$this->tester->assertTrue($model->sendSmsAboutCreateToAgent());
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

		$smsId = $job->run();
		$this->tester->assertNotEmpty($smsId);
		$this->tester->seeRecord(IssueNote::class, [
			'issue_id' => $settlement->getIssueId(),
			'user_id' => $model->getOwner(),
			'description' => $job->message->getMessage(),
			'type' => IssueNote::genereateSmsType($job->message->getDst(), $smsId),
		]);
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
}
