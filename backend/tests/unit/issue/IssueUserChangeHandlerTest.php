<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueUserChangeHandler;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\event\IssueUserEvent;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;

class IssueUserChangeHandlerTest extends Unit {

	private const USER_ID = UserFixtureHelper::AGENT_AGNES_MILLER;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::note(),
			IssueFixtureHelper::users(true),
			SettlementFixtureHelper::settlement(),
			ProvisionFixtureHelper::provision(),

		);
	}

	public function _before() {
		parent::_before();
		IssueUserEvent::on(Issue::class, IssueUserEvent::WILDCARD_EVENT, function (IssueUserEvent $event) {
			$handler = new IssueUserChangeHandler($event);
			$handler->user_id = static::USER_ID;
			$handler->parse();
		});
	}

	public function testCreateNew(): void {
		/**
		 * @var Issue $issue
		 */
		$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$issue->linkUser(UserFixtureHelper::AGENT_AGNES_MILLER, IssueUser::TYPE_VINDICATOR);
		$this->tester->seeRecord(IssuePayCalculation::class, [
			'issue_id' => $issue->id,
			'problem_status' => IssuePayCalculation::PROBLEM_STATUS_PROVISION_CONTROL,
		]);
		$this->tester->seeRecord(IssueNote::class, [
			'issue_id' => $issue->id,
			'title' => 'Add Larson Erika as vindicator.',
		]);

		$this->tester->seeEmailIsSent();
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('Change User in Issue: 1/11/2022. Add Larson Erika as vindicator.', $email->getSubject());
	}

	public function testUpdate(): void {
		/**
		 * @var Issue $issue
		 */
		$issue = $this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0);
		$issue->linkUser(UserFixtureHelper::AGENT_AGNES_MILLER, IssueUser::TYPE_AGENT);
		$this->tester->seeRecord(IssuePayCalculation::class, [
			'issue_id' => $issue->id,
			'problem_status' => IssuePayCalculation::PROBLEM_STATUS_PROVISION_CONTROL,
		]);
		$this->tester->seeRecord(IssueNote::class, [
			'issue_id' => $issue->id,
			'title' => 'Update Larson Erika as agent.',
		]);

		$this->tester->seeEmailIsSent();
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('Change User in Issue: 1/11/2022. Update Larson Erika as agent.', $email->getSubject());
	}
}
