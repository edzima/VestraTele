<?php

namespace common\tests\unit\jobs;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueNote;
use console\jobs\IssueSmsSendJob;

class IssueSmsSendJobTest extends SmsSendJobTest {

	protected string $jobClass = IssueSmsSendJob::class;

	private const OWNER_ID = UserFixtureHelper::AGENT_PETER_NOWAK;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(true),
		);
	}

	public function testExecute(): void {
		$this->giveJob([
			'issue_id' => 1,
			'owner_id' => static::OWNER_ID,
			'note_title' => 'Test Note Title',
		]);
		$this->tester->wantTo('Customer Phone');
		$this->job->message->setDst('48673222110');

		$id = $this->whenRun();

		$this->tester->assertNotEmpty($id);
		$this->tester->seeRecord(IssueNote::class, [
			'issue_id' => 1,
			'title' => 'Test Note Title - client: Wayne John[+48 673 222 110]',
			'description' => static::DEFAULT_MESSAGE_TEXT,
			'type' => IssueNote::genereateSmsType($this->job->message->getDst(), $id),
		]);
	}

	public function testExecuteWhenDontSend(): void {
		$this->giveJob([
			'issue_id' => 1,
			'owner_id' => UserFixtureHelper::AGENT_PETER_NOWAK,
			'note_title' => 'Test Note Title',
		]);
		parent::testExecuteWhenDontSend();
	}

}
