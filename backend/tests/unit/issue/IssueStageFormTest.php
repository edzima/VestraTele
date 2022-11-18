<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueStageForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueStage;
use common\tests\_support\UnitModelTrait;
use DateTime;
use yii\base\Model;

class IssueStageFormTest extends Unit {

	use UnitModelTrait;

	private IssueStageForm $model;
	private IssueFixtureHelper $issueFixtureHelper;

	public function _before() {
		parent::_before();
		$this->issueFixtureHelper = new IssueFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::entityResponsible(),
			IssueFixtureHelper::stageAndTypesFixtures()
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Name cannot be blank.', 'name');
		$this->thenSeeError('Shortname cannot be blank.', 'short_name');
		$this->thenSeeError('Types cannot be blank.', 'typesIds');
	}

	private function giveModel(array $config = []): void {
		$this->model = new IssueStageForm($config);
	}

	public function testSimpleSave(): void {
		$this->giveModel([
			'name' => 'Test Stage',
			'short_name' => 'TS',
			'typesIds' => [1],
		]);

		$this->thenSuccessSave();
		$this->thenSeeStage([
			'name' => 'Test Stage',
			'short_name' => 'TS',
		]);

		/**
		 * @var IssueStage $stage
		 */
		$stage = $this->tester->grabRecord(IssueStage::class, [
			'name' => 'Test Stage',
		]);

		$types = $stage->types;

		$this->tester->assertNotEmpty($types);
		$type = reset($types);
		$this->tester->assertSame(1, $type->id);
	}

	private function thenSeeStage(array $attributes): void {
		$this->tester->seeRecord(IssueStage::class, $attributes);
	}

	public function testNotExistedTypes(): void {
		$this->giveModel([
			'typesIds' => [1122112],
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Types is invalid.', 'typesIds');
	}

	public function testNotUniqueName(): void {
		$this->giveModel([
			'name' => 'Test Stage',
			'short_name' => 'TS',
			'typesIds' => [1],
		]);

		$this->thenSuccessSave();

		$this->giveModel([
			'name' => 'Test Stage',
			'short_name' => 'TS2',
			'typesIds' => [2],
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Name "Test Stage" has already been taken.', 'name');
	}

	public function testNotUniqueShortName(): void {
		$this->giveModel([
			'name' => 'Test Stage',
			'short_name' => 'TS',
			'typesIds' => [1],
		]);

		$this->thenSuccessSave();

		$this->giveModel([
			'name' => 'Test Stage 2',
			'short_name' => 'TS',
			'typesIds' => [2],
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Shortname "TS" has already been taken.', 'short_name');
	}

	public function testUpdateDayReminder(): void {
		$this->giveModel([
			'name' => 'Test Stage',
			'short_name' => 'TS',
			'typesIds' => [1],
			'days_reminder' => 5,
		]);

		$this->thenSuccessSave();

		$this->thenSeeStage([
			'name' => 'Test Stage',
			'days_reminder' => 5,
		]);

		$createdStage = $this->model->getModel();

		$issueWithStageChangeAt = $this->issueFixtureHelper->haveIssue([
			'stage_id' => $createdStage->id,
			'stage_change_at' => '2020-02-01 10:00:00',
		], false);

		$issueWithoutStageChangeAt = $this->issueFixtureHelper->haveIssue([
			'stage_id' => $createdStage->id,
		], false);

		$this->giveModel([
			'model' => $createdStage,
			'days_reminder' => 10,
		]);

		$this->thenSuccessSave();

		$this->tester->seeRecord(Issue::class, [
			'id' => $issueWithStageChangeAt,
			'stage_deadline_at' => '2020-02-11 00:00:00',
		]);

		/**
		 * @var Issue $issue
		 */
		$issue = $this->tester->grabRecord(Issue::class, ['id' => $issueWithoutStageChangeAt]);

		$date = new DateTime($issue->created_at);
		$date->modify("+ 10 days");

		$this->tester->seeRecord(Issue::class, [
			'id' => $issueWithoutStageChangeAt,
			'stage_deadline_at' => $date->format('Y-m-d H:i:s'),
		]);
	}

	public function getModel(): Model {
		return $this->model;
	}

	public function testUpdateDayReminderAsNull(): void {
		$this->giveModel([
			'name' => 'Test Stage',
			'short_name' => 'TS',
			'typesIds' => [1],
			'days_reminder' => 5,
		]);

		$this->thenSuccessSave();

		$this->thenSeeStage([
			'name' => 'Test Stage',
			'days_reminder' => 5,
		]);

		$createdStage = $this->model->getModel();

		$issueWithStageChangeAt = $this->issueFixtureHelper->haveIssue([
			'stage_id' => $createdStage->id,
			'stage_change_at' => '2020-02-01 10:00:00',
		], false);

		$issueWithoutStageChangeAt = $this->issueFixtureHelper->haveIssue([
			'stage_id' => $createdStage->id,
		], false);

		$this->giveModel([
			'model' => $createdStage,
			'days_reminder' => null,
		]);

		$this->thenSuccessSave();

		$this->tester->seeRecord(Issue::class, [
			'id' => $issueWithStageChangeAt,
			'stage_deadline_at' => null,
		]);

		$this->tester->seeRecord(Issue::class, [
			'id' => $issueWithoutStageChangeAt,
			'stage_deadline_at' => null,
		]);
	}
}
