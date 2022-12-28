<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueStage;
use backend\modules\issue\models\StageTypeForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueStageType;
use common\models\issue\IssueType;
use common\tests\_support\UnitModelTrait;
use DateTime;

class StageTypeFormTest extends Unit {

	use UnitModelTrait;

	private StageTypeForm $model;
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
		$this->giveModel([]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Type cannot be blank.', 'type_id');
		$this->thenSeeError('Stage cannot be blank.', 'stage_id');
	}

	private function giveModel(array $config = []): void {
		$this->model = new StageTypeForm($config);
	}

	public function testUpdateFromFixtures(): void {
		$this->tester->seeRecord(IssueStageType::class, [
			'type_id' => 1,
			'stage_id' => 1,
			'days_reminder' => 2,
		]);
		$this->giveModel(
			[
				'type_id' => 1,
				'stage_id' => 1,
				'days_reminder' => 3,
			]
		);
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssueStageType::class, [
			'type_id' => 1,
			'stage_id' => 1,
			'days_reminder' => 3,
		]);
	}

	public function testUpdateDayReminderAsNull(): void {
		$stageId = $this->tester->haveRecord(IssueStage::class, [
			'name' => 'Test Stage',
			'short_name' => 'TS',
		]);

		$typeLinkedId = $this->tester->haveRecord(IssueType::class, [
			'name' => 'Linked Type with Stage',
			'short_name' => 'L',
		]);

		$typeNotLinkedId = $this->tester->haveRecord(IssueType::class, [
			'name' => 'Not Linked Type with Stage',
			'short_name' => 'NL',
		]);

		IssueType::getTypes(true);
		IssueStage::getStages(true);

		$this->giveModel([
			'stage_id' => $stageId,
			'type_id' => $typeLinkedId,
		]);

		$this->thenSuccessSave();

		$issueWithLinkedTypeAndWithStageDeadlineAt = $this->issueFixtureHelper->haveIssue([
			'stage_id' => $stageId,
			'stage_deadline_at' => '2020-01-05 00:00:00',
			'type_id' => $typeLinkedId,
		], false);

		$issueWithoutLinkedTypeAndWithStageDeadline = $this->issueFixtureHelper->haveIssue([
			'stage_id' => $stageId,
			'type_id' => $typeNotLinkedId,
			'stage_deadline_at' => '2020-01-05 00:00:00',
		], false);

		$this->giveModel([
			'stage_id' => $stageId,
			'type_id' => $typeLinkedId,
			'days_reminder' => null,
		]);

		$this->thenSuccessSave();

		$this->tester->seeRecord(Issue::class, [
			'id' => $issueWithLinkedTypeAndWithStageDeadlineAt,
			'stage_deadline_at' => null,
		]);

		$this->tester->seeRecord(Issue::class, [
			'id' => $issueWithoutLinkedTypeAndWithStageDeadline,
			'stage_deadline_at' => '2020-01-05 00:00:00',
		]);
	}

	public function testUpdateDayReminder(): void {
		$stageId = $this->tester->haveRecord(IssueStage::class, [
			'name' => 'Test Stage',
			'short_name' => 'TS',
		]);

		$typeLinkedId = $this->tester->haveRecord(IssueType::class, [
			'name' => 'Linked Type with Stage',
			'short_name' => 'L',
		]);

		$typeNotLinkedId = $this->tester->haveRecord(IssueType::class, [
			'name' => 'Not Linked Type with Stage',
			'short_name' => 'NL',
		]);

		$this->giveModel([
			'stage_id' => $stageId,
			'type_id' => $typeLinkedId,
		]);

		$this->thenSuccessSave();

		$issueWithLinkedTypeAndWithStageChangeAt = $this->issueFixtureHelper->haveIssue([
			'stage_id' => $stageId,
			'stage_change_at' => '2020-02-01 10:00:00',
			'type_id' => $typeLinkedId,
		], false);

		$issueWithLinkedTypeAndWithoutStageChangeAt = $this->issueFixtureHelper->haveIssue([
			'stage_id' => $stageId,
			'type_id' => $typeLinkedId,
		], false);

		$issueWithoutLinkedTypeAndWithStageChangeAt = $this->issueFixtureHelper->haveIssue([
			'stage_id' => $stageId,
			'stage_change_at' => '2020-02-01 10:00:00',
			'type_id' => $typeNotLinkedId,
			'stage_deadline_at' => '2020-01-05 00:00:00',
		], false);

		$this->giveModel([
			'stage_id' => $stageId,
			'type_id' => $typeLinkedId,
			'days_reminder' => 10,
		]);

		$this->thenSuccessSave();

		$this->tester->seeRecord(Issue::class, [
			'id' => $issueWithLinkedTypeAndWithStageChangeAt,
			'stage_deadline_at' => '2020-02-11 00:00:00',
		]);

		$this->tester->seeRecord(Issue::class, [
			'id' => $issueWithoutLinkedTypeAndWithStageChangeAt,
			'stage_deadline_at' => '2020-01-05 00:00:00',
		]);

		/**
		 * @var Issue $issue
		 */
		$issue = $this->tester->grabRecord(Issue::class, ['id' => $issueWithLinkedTypeAndWithoutStageChangeAt]);

		$date = new DateTime($issue->created_at);
		$date->modify("+ 10 days");

		$this->tester->seeRecord(Issue::class, [
			'id' => $issueWithLinkedTypeAndWithoutStageChangeAt,
			'stage_deadline_at' => $date->format('Y-m-d H:i:s'),
		]);
	}

	public function getModel(): StageTypeForm {
		return $this->model;
	}
}
