<?php

namespace common\tests\unit\message;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\models\issue\IssueStage;
use common\models\message\IssueCreateMessagesForm;
use common\models\message\IssueStageChangeMessagesForm;

/**
 * @property-read IssueStageChangeMessagesForm $model
 */
class IssueStageChangeMessagesFormTest extends BaseIssueMessagesFormTest {

	protected const MODEL_CLASS = IssueStageChangeMessagesForm::class;

	public function _fixtures(): array {
		return array_merge(
			parent::_fixtures(),
			IssueFixtureHelper::entityResponsible(),
			IssueFixtureHelper::stages(),
		);
	}

	protected function messageTemplateFixtureDir(): string {
		return MessageTemplateFixtureHelper::DIR_ISSUE_STAGE_CHANGE;
	}

	public function keysProvider(): array {
		return [
			'Email Workers Without Issue Types' => [
				IssueStageChangeMessagesForm::generateKey(
					IssueStageChangeMessagesForm::TYPE_EMAIL,
					IssueCreateMessagesForm::keyWorkers(),
				),
				'email.issue.stageChange.workers',
			],
			'SMS Customer With Issue Types' => [
				IssueStageChangeMessagesForm::generateKey(
					IssueStageChangeMessagesForm::TYPE_SMS,
					IssueStageChangeMessagesForm::keyWorkers(),
					[1, 2]
				),
				'sms.issue.stageChange.workers.' . IssueStageChangeMessagesForm::issueTypesKeyPart([1, 2]),
			],
			'SMS Customer With Stage ID without Issue Types' => [
				IssueStageChangeMessagesForm::generateKey(
					IssueStageChangeMessagesForm::TYPE_SMS,
					IssueStageChangeMessagesForm::keyCustomer(),
					[],
					1
				),
				'sms.issue.stageChange.customer.stageID:1',
			],
			'SMS Customer With Stage ID and Issue Types' => [
				IssueStageChangeMessagesForm::generateKey(
					IssueStageChangeMessagesForm::TYPE_SMS,
					IssueStageChangeMessagesForm::keyCustomer(),
					[1, 2],
					1
				),
				'sms.issue.stageChange.customer.stageID:1.' . IssueStageChangeMessagesForm::issueTypesKeyPart([1, 2]),
			],
		];
	}

	public function testReminderDayKeys(): void {
		$templates = IssueStageChangeMessagesForm::daysReminderTemplates();

		$customers = $templates[IssueStageChangeMessagesForm::KEY_CUSTOMER];
		$workers = $templates[IssueStageChangeMessagesForm::KEY_WORKERS];

		$this->tester->assertNotEmpty($customers);
		$this->tester->assertEmpty($workers);

		foreach ($customers as $key => $template) {
			if ($key === 'sms.issue.stageChange.customer.reminderDays:7.stageID:1') {
				$this->tester->assertSame(7, IssueStageChangeMessagesForm::getDaysReminder($key));
				$this->tester->assertSame(1, IssueStageChangeMessagesForm::getStageID($key));

				$this->tester->assertEmpty(IssueStageChangeMessagesForm::findIssues($key)->all());

				$this->issueFixture->haveIssue([
					'stage_change_at' => date(DATE_ATOM, strtotime('-7 day')),
					'type_id' => 1,
					'stage_id' => 1,
				]);

				$issue = IssueStageChangeMessagesForm::findIssues($key)->one();

				$this->tester->assertNotNull($issue);
				$this->issue = $issue;
				$this->giveModel();
				$this->model->setCustomerSMSTemplate($template);
				$this->model->getSmsToCustomer();
				$this->tester->assertNotNull($this->model->getSmsToCustomer());
			}
		}

		$counters = IssueStageChangeMessagesForm::pushDelayedMessages(1);
		$this->tester->assertSame(1, $counters['customersSMS']);
		$this->tester->assertSame(0, $counters['customersEmail']);
		$this->tester->assertSame(0, $counters['agentsSMS']);
		$this->tester->assertSame(0, $counters['agentsEmail']);
	}

	public function testFindIssues(): void {
		$key = IssueStageChangeMessagesForm::generateKey(
			IssueStageChangeMessagesForm::TYPE_SMS,
			IssueStageChangeMessagesForm::keyCustomer(),
			[1, 2],
			1
		);

		$this->templateFixture->save($key);
		$query = IssueStageChangeMessagesForm::findIssues($key);
		$this->tester->assertGreaterThan(0, $query->count());

		foreach ($query->all() as $issue) {
			$this->tester->assertSame(1, $issue->getIssueStage()->id);
			$this->tester->assertContains($issue->getIssueType()->id, [1, 2]);
		}

		$key = IssueStageChangeMessagesForm::generateKey(
			IssueStageChangeMessagesForm::TYPE_SMS,
			IssueStageChangeMessagesForm::keyCustomer(),
			[1, 2],
			1,
			1
		);

		$this->templateFixture->save($key);
		$query = IssueStageChangeMessagesForm::findIssues($key);
		$this->tester->assertSame(0, (int) $query->count());

		$this->issueFixture->haveIssue([
			'stage_change_at' => date(DATE_ATOM, strtotime('-1 day')),
			'type_id' => 1,
			'stage_id' => 1,
		]);

		$this->issueFixture->haveIssue([
			'stage_change_at' => date(DATE_ATOM, strtotime('+1 day')),
			'type_id' => 1,
			'stage_id' => 1,
		]);

		$this->issueFixture->haveIssue([
			'stage_change_at' => date(DATE_ATOM, strtotime('-2 day')),
			'type_id' => 1,
			'stage_id' => 1,
		]);

		// other stage_id from create key.
		$this->issueFixture->haveIssue([
			'stage_change_at' => date(DATE_ATOM, strtotime('-1 day')),
			'type_id' => 1,
			'stage_id' => 2,
		]);

		// other type_id from create key.

		$this->issueFixture->haveIssue([
			'stage_change_at' => date(DATE_ATOM, strtotime('-1 day')),
			'type_id' => 3,
			'stage_id' => 1,
		]);

		$this->tester->assertSame(1, (int) $query->count());

		$issue = $query->one();
		$this->tester->assertSame(1, $issue->getIssueStage()->id);
		$this->tester->assertSame(1, $issue->getIssueType()->id);
	}

	public function testTemplateFromFixture(): void {
		$this->giveIssue();
		$this->giveModel();

		$message = $this->model->getEmailToCustomer();
		$this->tester->assertNull($message);

		$message = $this->model->getEmailToWorkers();
		$this->tester->assertNull($message);

		$this->model->withStageIdKey = false;

		$message = $this->model->getEmailToWorkers();
		$this->tester->assertNotNull($message);
	}

	public function testStagesNamesInEmails(): void {
		$this->giveIssue();
		$this->giveModel();

		$this->model->withStageIdKey = false;

		$message = $this->model->getEmailToWorkers();

		$this->tester->assertStringContainsString($this->issue->getIssueName(), $message->getSubject());
		$this->tester->assertStringContainsString($this->issue->getIssueStage()->name, $message->getSubject());
		$this->tester->assertMessageBodyContainsString($this->issue->getIssueStage()->name, $message);
	}

	public function testStagesNamesWithPreviousStageInEmails(): void {
		$this->giveIssue();
		$this->giveModel();

		$this->model->withStageIdKey = false;
		$this->model->previousStage = IssueStage::getStages()[array_rand(IssueStage::getStages())];

		$message = $this->model->getEmailToWorkers();
		$this->tester->assertStringContainsString($this->issue->getIssueStage()->name, $message->getSubject());
		$this->tester->assertMessageBodyContainsString($this->issue->getIssueStage()->name, $message);

		$this->tester->assertStringContainsString($this->model->previousStage->name, $message->getSubject());
		$this->tester->assertMessageBodyContainsString($this->model->previousStage->name, $message);
	}

}
