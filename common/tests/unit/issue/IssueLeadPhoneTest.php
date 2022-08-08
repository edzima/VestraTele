<?php

namespace common\tests\unit\issue;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\LeadFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\form\IssueLeadPhone;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\tests\unit\Unit;
use yii\helpers\Console;

class IssueLeadPhoneTest extends Unit {

	private IssueLeadPhone $model;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(true),
			LeadFixtureHelper::lead(),
		);
	}

	public function testEmptyPhone(): void {
		$this->giveModel('');
		foreach ($this->model->findLeads()->all() as $lead) {
			$this->tester->assertEmpty($lead->getPhone());
		}
		foreach ($this->model->findIssues()->all() as $issue) {
			$users = array_filter($issue->users, static function (IssueUser $user): bool {
				return !$user->user->profile->hasPhones();
			});
			$this->tester->assertNotEmpty($users);
		}
	}

	public function testPhoneAsArray(): void {
		$this->giveModel([
			'48777-222-122',
			'+48 673 222 110',
		]);
		$this->model->validate();
		foreach ($this->model->findLeads()->all() as $lead) {
			$this->tester->assertNotEmpty($lead->getPhone());
		}
		foreach ($this->model->findIssues()->all() as $issue) {
			$users = array_filter($issue->users, static function (IssueUser $user): bool {
				return !$user->user->profile->hasPhones();
			});
			$this->tester->assertNotEmpty($users);
		}
	}

	public function testGetUserPhones(): void {
		$phones = IssueLeadPhone::getUserPhones($this->tester->grabFixture(UserFixtureHelper::CUSTOMER, 0));
		$this->tester->assertCount(2, $phones);
		$this->tester->assertContains('+48 673 222 110', $phones);
		$this->tester->assertContains('+48 673 222 220', $phones);

		$phones = IssueLeadPhone::getUserPhones($this->tester->grabFixture(UserFixtureHelper::CUSTOMER, 1));
		$this->tester->assertCount(1, $phones);
		$this->tester->assertContains('+48 682 333 110', $phones);
	}

	public function testIssueUserTypes(): void {
		$this->giveModel();
		$this->model->setIssue($this->tester->grabFixture(IssueFixtureHelper::ISSUE, 0));
		$this->tester->assertNotEmpty($this->model->phone);
	}

	public function testIssueFromLead(): void {
		foreach (Issue::find()
			->andWhere(['lead_id' => null])
			->with('users.user.userProfile')
			->batch() as $rows) {
			foreach ($rows as $row) {
				/** @var Issue $row */
				$model = new IssueLeadPhone();
				$model->setIssue($row);
				codecept_debug($model->phone);
				if (!empty($model->phone)) {
					$leadsCount = $model->findLeads()->count();
					if ($leadsCount) {
						Console::output($row->getIssueName() . ' has Leads: ' . $leadsCount);
					}
				}
			}
		}
	}

	private function giveModel($phone = null): void {
		$this->model = new IssueLeadPhone([
			'phone' => $phone,
		]);
	}
}
