<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueStageChangeForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use common\tests\_support\UnitModelTrait;

class IssueStageChangeFormTest extends Unit {

	use UnitModelTrait;

	private IssueStageChangeForm $model;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::stageAndTypesFixtures(),
			IssueFixtureHelper::note(),
			IssueFixtureHelper::agent()
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessSave();
		$this->thenSeeError('Date At cannot be blank.', 'date_at');
		$this->thenSeeError('New Stage must be other than old.', 'stage_id');
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
