<?php

namespace common\tests\unit\message;

use common\components\message\MessageTemplateKeyHelper;
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
			IssueFixtureHelper::stages()
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
				'email.issue.stageChange.workers.' . MessageTemplateKeyHelper::issueTypesKeyPart([1, 2]),
			],
		];
	}

	public function testStagesNamesInEmails(): void {
		$this->giveIssue();
		$this->giveModel();

		$message = $this->model->getEmailToCustomer();
		$this->tester->assertNull($message);

		$message = $this->model->getEmailToWorkers();
		$this->tester->assertStringContainsString($this->issue->getIssueName(), $message->getSubject());
		$this->tester->assertStringContainsString($this->issue->getIssueStage()->name, $message->getSubject());
		$this->tester->assertMessageBodyContainsString($this->issue->getIssueStage()->name, $message);
	}

	public function testStagesNamesWithPreviousStageInEmails(): void {
		$this->giveIssue();
		$this->giveModel();
		$this->model->previousStage = IssueStage::getStages()[array_rand(IssueStage::getStages())];
		$message = $this->model->getEmailToCustomer();
		$this->tester->assertNull($message);

		$message = $this->model->getEmailToWorkers();
		$this->tester->assertStringContainsString($this->issue->getIssueStage()->name, $message->getSubject());
		$this->tester->assertMessageBodyContainsString($this->issue->getIssueStage()->name, $message);

		$this->tester->assertStringContainsString($this->model->previousStage->name, $message->getSubject());
		$this->tester->assertMessageBodyContainsString($this->model->previousStage->name, $message);
	}

}
