<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\LeadFixtureHelper;
use common\models\issue\IssueMeet;
use common\modules\lead\components\LeadManager;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\LeadCampaign;
use common\tests\unit\Unit;
use console\components\MeetToLeadCreator;
use yii\helpers\Json;

/**
 * @deprecated
 */
class MeetToLeadTest extends Unit {

	private array $creatorConfig = [
		'emptyCampaignName' => 'Self',
	];

	private IssueMeet $meet;
	private MeetToLeadCreator $creator;
	private LeadForm $lead;
	private ?ActiveLead $activeLead = null;
	private ?ReportForm $report = null;
	private LeadManager $manager;

	public function _before() {
		parent::_before();
		$this->creator = new MeetToLeadCreator($this->creatorConfig);
		$this->manager = new LeadManager();
	}

	public function _fixtures(): array {
		return
			array_merge(
				IssueFixtureHelper::types(),
				LeadFixtureHelper::leads(),
				LeadFixtureHelper::campaign(),
				LeadFixtureHelper::question(),
			);
	}

	public function testLeadData(): void {
		$this->giveMeet([
			'details' => 'Some details',
		]);

		$this->whenCreateLead();

		$this->tester->assertSame(Json::encode($this->meet->getAttributes()), $this->lead->data);
	}

	public function testWithoutCampaign(): void {
		$this->creator->emptyCampaignName = 'Self';

		$this->giveMeet([
			'agent_id' => 1,
		]);

		$this->whenCreateLead();

		$campaignId = $this->lead->campaign_id;

		$this->tester->seeRecord(LeadCampaign::class, [
			'name' => 'Self',
			'owner_id' => 1,
			'id' => $campaignId,
		]);

		$this->giveMeet([
			'agent_id' => 1,
		]);

		$this->whenCreateLead();

		$this->tester->seeRecord(LeadCampaign::class, [
			'name' => 'Self',
			'owner_id' => 1,
			'id' => $campaignId,
		]);

		$this->giveMeet([
			'agent_id' => 2,
		]);

		$this->whenCreateLead();

		$this->tester->seeRecord(LeadCampaign::class, [
			'name' => 'Self',
			'owner_id' => 2,
			'id' => $this->lead->campaign_id,

		]);
	}

	public function testReportWithoutAgentAndTele(): void {
		$this->giveMeet([
		]);

		$this->whenCreateLead();
		$this->whenPush();
		$this->whenCreateReport();

		$this->thenReportIsNull();
	}

	public function testReportWithAgent(): void {
		$this->giveMeet([
			'agent_id' => 1,
		]);

		$this->whenCreateLead();
		$this->whenPush();
		$this->whenCreateReport();

		$this->tester->assertSame(1, $this->report->owner_id);
	}

	public function testReportWithTeleWithoutAgent(): void {
		$this->giveMeet([
			'tele_id' => 1,
		]);

		$this->whenCreateLead();
		$this->whenPush();
		$this->whenCreateReport();

		$this->tester->assertSame(1, $this->report->owner_id);
	}

	private function giveMeet(array $config = []): void {
		if (!isset($config['status'])) {
			$config['status'] = IssueMeet::STATUS_NEW;
		}
		if (!isset($config['type_id'])) {
			$config['type_id'] = 1;
		}
		if (!isset($config['email'])) {
			$config['email'] = 'test@email.com';
		}
		$this->meet = new IssueMeet($config);
	}

	private function whenCreateLead(): void {
		$this->lead = $this->creator->createLead($this->meet);
	}

	private function whenPush(): void {
		$this->activeLead = $this->manager->pushLead($this->lead);
	}

	private function whenCreateReport(): void {
		$this->report = $this->creator->createReport($this->activeLead, $this->meet);
	}

	private function thenReportIsNull(): void {
		$this->tester->assertNull($this->report);
	}
}
