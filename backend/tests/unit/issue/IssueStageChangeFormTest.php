<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueStage;
use backend\modules\issue\models\IssueStageChangeForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\tests\_support\UnitModelTrait;
use yii\mail\MessageInterface;

class IssueStageChangeFormTest extends Unit {

	use UnitModelTrait;

	private IssueStageChangeForm $model;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			IssueFixtureHelper::note(),
			IssueFixtureHelper::users(true),
			IssueFixtureHelper::linkedIssues(),
			MessageTemplateFixtureHelper::fixture(MessageTemplateFixtureHelper::DIR_ISSUE_STAGE_CHANGE),
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessSave();
		$this->thenSeeError('Date At cannot be blank.', 'date_at');
		$this->thenSeeError('New Stage must be other than old.', 'stage_id');
	}

	public function testArchiveWithoutArchiveNr(): void {
		$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$this->giveModel($issue);
		$this->model->stage_id = IssueStage::ARCHIVES_ID;
		$this->thenUnsuccessSave();
		$this->thenSeeError('Archives cannot be blank.', 'archives_nr');
	}

	public function testLinkedIssueWithNotLinkedId(): void {
		/** @var Issue $issue */
		$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$this->giveModel($issue);
		$this->model->linkedIssues = [3];
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Linked Issues is invalid.', 'linkedIssues');
	}

	public function testNotTypeStage(): void {
		$this->giveModel(Issue::find()->andWhere([
			'type_id' => 1,
			'stage_id' => 1,
		])->one());
		$this->model->stage_id = 3;
		$this->thenUnsuccessSave();
		$this->thenSeeError('Stage is invalid.', 'stage_id');
	}

	public function testValid(): void {
		$this->giveModel(Issue::find()->andWhere([
			'type_id' => 1,
			'stage_id' => 1,
		])->one());
		$this->model->stage_id = 2;
		$this->model->date_at = date($this->model->dateFormat);
		$this->model->user_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->thenSuccessSave();
		$this->tester->seeRecord(Issue::class, [
			'id' => $this->model->getIssue()->getIssueId(),
			'stage_id' => 2,
		]);

		/** @var IssueNote $note */
		$note = $this->tester->grabRecord(IssueNote::class, [
			'issue_id' => $this->model->getIssue()->getIssueId(),
			'title' => 'Proposal (previous: Completing documents)',
		]);
		$this->assertNotNull($note);
		$this->assertTrue($note->isForStageChange());
		$this->model->save();
	}

	public function testValidWithLinked(): void {
		/** @var Issue $issue */
		$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$this->giveModel($issue);
		$this->model->linkedIssues = [4];
		$this->model->user_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->model->date_at = date($this->model->dateFormat);
		$this->model->stage_id = 2;

		$this->tester->dontSeeRecord(Issue::class, [
			'stage_id' => 2,
			'id' => 4,
		]);
		$this->thenSuccessSave();

		$this->tester->seeRecord(Issue::class, [
			'stage_id' => 2,
			'id' => 4,
		]);
	}

	public function testPushMessagesForStageIdTemplate(): void {
		$this->giveModel(Issue::find()->andWhere([
			'type_id' => 1,
			'stage_id' => 1,
		])->one());
		$this->model->stage_id = 2;
		$this->model->date_at = date($this->model->dateFormat);
		$this->model->user_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->thenSuccessSave();
		$this->tester->assertTrue($this->model->pushMessages());
		/**
		 * @var MessageInterface $email
		 */
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertStringContainsString('Global Email to Workers', $email->getSubject());
	}

	public function testPushMessagesForStageIdWithoutTemplate(): void {
		$this->giveModel(Issue::find()->andWhere([
			'type_id' => 1,
			'stage_id' => 2,
		])->one());
		$this->model->stage_id = 1;
		$this->model->date_at = date($this->model->dateFormat);
		$this->model->user_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->thenSuccessSave();

		$this->tester->assertTrue($this->model->pushMessages());
		/**
		 * @var MessageInterface $email
		 */
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertStringContainsString('Dedicated Email Subject for Stage: Completing Documents', $email->getSubject());
	}

	public function getModel(): IssueStageChangeForm {
		return $this->model;
	}

	private function giveModel(IssueInterface $issue = null) {
		if ($issue === null) {
			$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		}
		$this->model = new IssueStageChangeForm($issue);
	}
}
