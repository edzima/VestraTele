<?php

namespace common\tests\unit\message;

use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSettlement;
use common\models\message\IssueSettlementCreateMessagesForm;
use common\models\message\IssueSmsForm;
use common\models\user\User;
use console\jobs\IssueSmsSendJob;
use Yii;
use yii\helpers\ArrayHelper;
use yii\mail\MessageInterface;

/**
 * @property IssueSettlementCreateMessagesForm $model
 */
class IssueSettlementCreateMessagesFormTest extends BaseIssueMessagesFormTest {

	protected const MODEL_CLASS = IssueSettlementCreateMessagesForm::class;

	private const DEFAULT_SETTLEMENT_TYPE = IssueSettlement::TYPE_HONORARIUM;
	private const DEFAULT_SETTLEMENT_VALUE = 1000;

	private ?IssueSettlement $settlement = null;

	protected function messageTemplateFixtureDir(): string {
		return MessageTemplateFixtureHelper::DIR_ISSUE_SETTLEMENT_CREATE;
	}

	public function keysProvider(): array {
		return [
			'SMS Customer With Settlement Type and Issue Type' => [
				IssueSettlementCreateMessagesForm::generateKey(
					IssueSettlementCreateMessagesForm::TYPE_SMS,
					IssueSettlementCreateMessagesForm::keyCustomer(),
					[1, 2],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'sms.issue.settlement.create.customer.settlementType:30.issueTypes:1,2',
			],
			'SMS Workers With Settlement Type and Issue Type' => [
				IssueSettlementCreateMessagesForm::generateKey(
					IssueSettlementCreateMessagesForm::TYPE_SMS,
					IssueSettlementCreateMessagesForm::keyWorkers(),
					[1, 2],
					IssueSettlement::TYPE_HONORARIUM,
				),
				'sms.issue.settlement.create.workers.settlementType:30.issueTypes:1,2',
			],
		];
	}

	public function testDefaultCustomerSms(): void {
		$this->giveIssue();
		$this->giveModel();

		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertSame('Sms About Create Honorarium Settlement in Issue(TYPE_1) for Customer.', $sms->note_title);
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
		$value = $this->getFormattedValue(false);
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

	public function testSmsToAgentForHonorariumAndType1(): void {
		$this->giveIssue(1);
		$this->giveModel();
		$sms = $this->model->getSmsToAgent();
		$this->tester->assertNotNull($sms);
		$this->tester->assertStringContainsString($this->getCustomer()->getFullName(), $sms->message);
		$this->tester->assertStringContainsString($this->getCustomer()->getPhone(), $sms->message);
		$this->tester->assertStringContainsString($this->getFormattedValue(true), $sms->message);
	}

	public function testSmsToAgentForHonorariumAndType2(): void {
		$this->giveIssue(2);
		$this->giveModel();
		$sms = $this->model->getSmsToAgent();
		$this->tester->assertNull($sms);
	}

	public function testSmsToCustomerForHonorarium(): void {
		$this->giveModel();
		$sms = $this->model->getSmsToCustomer();
		$this->tester->assertNotNull($sms);
		$this->tester->assertStringContainsString($this->getAgent()->getFullName(), $sms->message);
		$this->tester->assertStringContainsString($this->getAgent()->getPhone(), $sms->message);
		$this->tester->assertStringNotContainsString($this->getFormattedValue(true), $sms->message);
	}

	protected function getFormattedValue(bool $replaceNonBreakSpace): string {
		$value = Yii::$app->formatter->asCurrency($this->settlement->getValue());
		if ($replaceNonBreakSpace) {
			$value = str_replace(["&nbsp;", ' '], ' ', $value);
		}
		return $value;
	}

	public function testWorkersEmails(): void {
		$this->giveModel();
		$message = $this->model->getEmailToWorkers();
		$this->tester->assertNotNull($message);
		$settlement = $this->settlement;
		$this->tester->assertArrayHasKey($this->getAgent()->email, $message->getTo());
		$this->tester->assertArrayHasKey($settlement->getIssueModel()->tele->email, $message->getTo());
		$this->tester->assertMessageBodyContainsString($settlement->getFrontendUrl(), $message);
		$this->tester->assertStringNotContainsString($this->getFormattedValue(false), $message);
	}

	protected function getCustomer(): User {
		return $this->settlement->getIssueModel()->customer;
	}

	protected function getAgent(): User {
		return $this->settlement->getIssueModel()->agent;
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
