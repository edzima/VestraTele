<?php

namespace backend\tests\functional\issue;

use backend\tests\Step\Functional\IssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\Customer;

class IssueCreateCest {

	protected const ROUTE_CREATE = '/issue/issue/create';

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures(): array {
		return IssueFixtureHelper::fixtures();
	}

	public function checkCreate(IssueManager $I): void {
		$I->amLoggedIn();
		/** @var Customer $customer */
		$customer = $I->grabFixture(IssueFixtureHelper::CUSTOMER, 0);

		$I->amOnRoute('/issue/issue/create', ['customerId' => $customer->id]);
		$I->see('Create issue for: ' . $customer, 'title');
		$I->submitForm('#issue-form', [
			'IssueForm' => [
				'agent_id' => 300,
				'entity_responsible_id' => 1,
				'type_id' => 1,
				'stage_id' => 1,
				'lawyer_id' => 200,
				'signing_at' => date('Y-m-d'),
			],
		]);

		/** @var Issue $issue */
		$issue = $I->grabRecord(Issue::class, [
			'entity_responsible_id' => 1,
			'type_id' => 1,
			'stage_id' => 1,
			'signing_at' => date('Y-m-d'),
		]);

		$I->seeRecord(IssueUser::class, [
			'issue_id' => $issue->id,
			'user_id' => $customer->id,
			'type' => IssueUser::TYPE_CUSTOMER,
		]);

		$I->seeRecord(IssueUser::class, [
			'issue_id' => $issue->id,
			'user_id' => 300,
			'type' => IssueUser::TYPE_AGENT,
		]);

		$I->seeRecord(IssueUser::class, [
			'issue_id' => $issue->id,
			'user_id' => 200,
			'type' => IssueUser::TYPE_LAWYER,
		]);

		$I->seeLink('Update');
	}

}
