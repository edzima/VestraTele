<?php

namespace common\tests\unit\lead;

use common\components\IssuesLeads;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\LeadFixtureHelper;
use common\models\issue\form\IssueLeadPhone;
use common\models\issue\Issue;
use common\models\issue\IssueType;
use common\modules\lead\models\LeadIssue;
use common\modules\lead\models\LeadSource;
use common\tests\unit\Unit;
use yii\base\InvalidConfigException;

class IssuesLeadsTest extends Unit {

	private IssuesLeads $leadIssue;
	private LeadFixtureHelper $leadFixtureHelper;
	private IssueFixtureHelper $issueFixtureHelper;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(true),
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::source(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::issue(),
		);
	}

	public function _before() {
		parent::_before();
		$this->leadIssue = new IssuesLeads();
		$this->leadFixtureHelper = new LeadFixtureHelper($this->tester);
		$this->issueFixtureHelper = new IssueFixtureHelper($this->tester);
	}

	public function testCreatePhoneModel(): void {
		$phone = $this->leadIssue->createPhoneModel();
		$this->tester->assertInstanceOf(IssueLeadPhone::class, $phone);

		$this->leadIssue->modelPhone = [
			'usersTypes' => [
				'one',
				'two',
			],
		];

		$phone = $this->leadIssue->createPhoneModel();
		$this->tester->assertSame([
			'one',
			'two',
		], $phone->usersTypes);
	}

	public function testMergeNotLinkedIssues(): void {
		$this->leadIssue->crmId = 1;
		$this->tester->assertNull($this->leadIssue->mergeNotLinkedIssues());

		//lead source: #1 has type_id: #1 @see Fixtures Data.
		/** @var Issue $issue */
		$issue = $this->tester->grabRecord(Issue::class, [
				'type_id' => $this->tester->grabRecord(IssueType::class, [
					'lead_type_id' => 1,
				])->id,
			]
		);

		$this->tester->assertNotNull($issue);
		$lead = $this->leadFixtureHelper->haveLead(
			[
				'phone' => $issue->customer->profile->phone,
				'source_id' => $this->tester->grabRecord(LeadSource::class, [
					'type_id' => 1,
				])->id,
			]
		);

		$this->tester->assertGreaterThan(0, $this->leadIssue->mergeNotLinkedIssues());

		$this->tester->seeRecord(LeadIssue::class, [
			'lead_id' => $lead,
			'issue_id' => $issue->getIssueId(),
			'crm_id' => 1,
		]);
	}

	public function testGetCRMIdWithoutSet(): void {
		$this->tester->expectThrowable(new InvalidConfigException('Lead CRM App Id must set.'), function () {
			$this->leadIssue->getCrmId();
		});
	}

}
