<?php

namespace common\tests\unit\issue;

use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueNote;
use common\models\issue\IssueNoteForm;
use common\tests\_support\UnitModelTrait;

class IssueNoteFormTest extends Unit {

	use UnitModelTrait;

	private IssueNoteForm $model;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::note(),
			IssueFixtureHelper::users(true),
			IssueFixtureHelper::linkedIssues(),
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('User cannot be blank.', 'user_id');
		$this->thenSeeError('Title cannot be blank.', 'title');
		$this->thenSeeError('Issue cannot be blank.', 'issue_id');
	}

	public function testNotExistedIssue(): void {
		$this->giveModel([
			'issue_id' => 1121212,
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Issue is invalid.', 'issue_id');
	}

	public function testNotExistedUser(): void {
		$this->giveModel([
			'user_id' => 1121212,
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('User is invalid.', 'user_id');
	}

	public function testCreate(): void {
		$this->giveModel([
			'issue_id' => 1,
			'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'title' => 'Some Title',
			'description' => 'Some Desc',
		]);
		$this->thenSuccessSave();
		$this->thenSeeNote([
			'issue_id' => 1,
			'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'title' => 'Some Title',
			'description' => 'Some Desc',
			'publish_at' => $this->model->publish_at,
		]);
	}

	public function testPinned(): void {
		$this->giveModel([
			'issue_id' => 1,
			'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'title' => 'Some Title',
			'description' => 'Some Desc',
			'is_pinned' => true,
		]);
		$this->thenSuccessSave();
		$this->thenSeeNote([
			'issue_id' => 1,
			'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'title' => 'Some Title',
			'description' => 'Some Desc',
			'publish_at' => $this->model->publish_at,
			'is_pinned' => true,
		]);
	}

	public function testDontPinned(): void {
		$this->giveModel([
			'issue_id' => 1,
			'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'title' => 'Some Title',
			'description' => 'Some Desc',
			'is_pinned' => false,
		]);
		$this->thenSuccessSave();
		$this->thenSeeNote([
			'issue_id' => 1,
			'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'title' => 'Some Title',
			'description' => 'Some Desc',
			'publish_at' => $this->model->publish_at,
			'is_pinned' => false,
		]);
	}

	public function testInvalidLinkedId(): void {
		$this->giveModel([
			'issue_id' => 1,
			'linkedIssues' => [3],
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Linked Issues is invalid.', 'linkedIssues');
	}

	public function testWithLinkedIssues(): void {
		$this->giveModel([
			'issue_id' => 1,
			'linkedIssues' => [2],
			'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'title' => 'Some Title',
			'description' => 'Some Desc',
		]);

		$this->thenSuccessSave();
		$this->thenSeeNote([
			'issue_id' => 1,
			'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'title' => 'Some Title',
			'description' => 'Some Desc',
			'publish_at' => $this->model->publish_at,
		]);
		$this->thenSeeNote([
			'issue_id' => 2,
			'user_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'title' => 'Some Title',
			'description' => 'Some Desc',
			'publish_at' => $this->model->publish_at,
		]);
	}

	public function testUpdateNoteWithoutUpdaterId(): void {
		$noteId = $this->tester->haveRecord(IssueNote::class, [
			'issue_id' => 1,
			'title' => 'Test Title for Update',
			'description' => 'Some Desc for Update',
			'user_id' => UserFixtureHelper::AGENT_EMILY_PAT,
		]);
		$this->giveModel([
			'model' => $this->tester->grabRecord(IssueNote::class, [
				'id' => $noteId,
			]),
		]);
		$this->thenUnsuccessSave();
		$this->thenSeeError('Updater cannot be blank.', 'updater_id');
	}

	public function testUpdateNoteWithDirtyTitle(): void {
		$noteId = $this->tester->haveRecord(IssueNote::class, [
			'issue_id' => 1,
			'title' => 'Test Title for Update',
			'description' => 'Some Desc for Update',
			'user_id' => UserFixtureHelper::AGENT_EMILY_PAT,
		]);
		$this->giveModel([
			'model' => $this->tester->grabRecord(IssueNote::class, [
				'id' => $noteId,
			]),
			'updater_id' => UserFixtureHelper::AGENT_EMILY_PAT,
		]);

		$this->model->title = 'Updated Title';
		$this->thenSuccessSave();
		$this->thenSeeNote(
			[
				'id' => $noteId,
				'title' => 'Updated Title',
				'updater_id' => UserFixtureHelper::AGENT_EMILY_PAT,

			]
		);
		$this->tester->assertTrue($this->model->hasDirtyTitleOrDescription());
	}

	public function testUpdateNoteWithDirtyDescription(): void {
		$noteId = $this->tester->haveRecord(IssueNote::class, [
			'issue_id' => 1,
			'title' => 'Test Title for Update',
			'description' => 'Some Desc for Update',
			'user_id' => UserFixtureHelper::AGENT_EMILY_PAT,
		]);
		$this->giveModel([
			'model' => $this->tester->grabRecord(IssueNote::class, [
				'id' => $noteId,
			]),
			'updater_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		]);

		$this->model->description = 'Updated Description';
		$this->thenSuccessSave();
		$this->thenSeeNote(
			[
				'id' => $noteId,
				'description' => 'Updated Description',
				'user_id' => UserFixtureHelper::AGENT_EMILY_PAT,
				'updater_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			]
		);
		$this->tester->assertTrue($this->model->hasDirtyTitleOrDescription());
	}

	public function testUpdateNoteWithoutDirtyTitleOrDescription(): void {
		$noteId = $this->tester->haveRecord(IssueNote::class, [
			'issue_id' => 1,
			'title' => 'Test Title for Update',
			'description' => 'Some Desc for Update',
			'user_id' => UserFixtureHelper::AGENT_EMILY_PAT,
		]);
		$this->giveModel([
			'model' => $this->tester->grabRecord(IssueNote::class, [
				'id' => $noteId,
			]),
			'updater_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
		]);

		$this->model->description = 'Some Desc for Update';
		$this->thenSuccessSave();
		$this->thenSeeNote(
			[
				'id' => $noteId,
				'description' => 'Some Desc for Update',
			]
		);
		$this->tester->assertFalse($this->model->hasDirtyTitleOrDescription());
	}

	private function giveModel(array $config = []): void {
		$this->model = new IssueNoteForm($config);
	}

	public function getModel(): IssueNoteForm {
		return $this->model;
	}

	private function thenSeeNote(array $attributes): void {
		$this->tester->seeRecord(IssueNote::class, $attributes);
	}
}
