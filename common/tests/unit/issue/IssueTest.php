<?php

namespace common\tests\unit\issue;

use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueStage;
use common\tests\unit\Unit;

class IssueTest extends Unit {

	private IssueFixtureHelper $issueFixtureHelper;

	public function _before() {
		parent::_before();
		$this->issueFixtureHelper = new IssueFixtureHelper($this->tester);
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::fixtures()
		);
	}

	public function testStageIsDelayedForStageWithoutDaysReminder(): void {
		$stageId = $this->haveStage(__METHOD__, null);

		/**
		 * @var Issue $issue
		 */
		$issue = $this->tester->grabRecord(Issue::class, [
			'id' => $this->issueFixtureHelper->haveIssue([
				'stage_id' => $stageId,
			]),
		]);

		$this->tester->assertNull($issue->hasDelayedStage());
	}

	public function testStageIsDelayedForStageWithDaysReminderWithoutStageChangeAt(): void {
		$stageId = $this->haveStage(__METHOD__, 3);

		/**
		 * @var Issue $issue
		 */
		$issue = $this->tester->grabRecord(Issue::class, [
			'id' => $this->issueFixtureHelper->haveIssue([
				'stage_id' => $stageId,
				'stage_change_at' => null,
			]),
		]);

		$this->tester->assertNull($issue->hasDelayedStage());
	}

	public function testHasDelayedStageForNotDelayed(): void {
		$stageId = $this->haveStage(__METHOD__, 3);

		/**
		 * @var Issue $issue
		 */
		$issue = $this->tester->grabRecord(Issue::class, [
			'id' => $this->issueFixtureHelper->haveIssue([
				'stage_id' => $stageId,
				'stage_change_at' => date('Y-m-d', strtotime('- 1 day')),
			]),
		]);

		$this->tester->assertFalse($issue->hasDelayedStage());
	}

	public function testHasDelayedStageForEqualDays(): void {
		$stageId = $this->haveStage(__METHOD__, 3);

		/**
		 * @var Issue $issue
		 */
		$issue = $this->tester->grabRecord(Issue::class, [
			'id' => $this->issueFixtureHelper->haveIssue([
				'stage_id' => $stageId,
				'stage_change_at' => date('Y-m-d', strtotime('- 3 days')),
			]),
		]);

		$this->tester->assertTrue($issue->hasDelayedStage());
	}

	public function testHasDelayedStageForUpperDays(): void {
		$stageId = $this->haveStage(__METHOD__, 3);

		/**
		 * @var Issue $issue
		 */
		$issue = $this->tester->grabRecord(Issue::class, [
			'id' => $this->issueFixtureHelper->haveIssue([
				'stage_id' => $stageId,
				'stage_change_at' => date('Y-m-d', strtotime('- 4 days')),
			]),
		]);

		$this->tester->assertTrue($issue->hasDelayedStage());
	}

	private function haveStage(string $name, ?int $daysReminder): int {
		return $this->tester->haveRecord(IssueStage::class, [
			'name' => $name,
			'days_reminder' => $daysReminder,
			'short_name' => substr($name, 0, 2),
		]);
	}
}
