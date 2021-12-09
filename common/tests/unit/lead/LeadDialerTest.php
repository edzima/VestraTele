<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\components\LeadDialer;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\Module;
use common\tests\helpers\LeadFactory;
use common\tests\unit\Unit;

class LeadDialerTest extends Unit {

	private const STATUS_CALLING = 2;
	private const STATUS_NOT_ANSWERED = 3;
	private const STATUS_ANSWERED = 4;
	private const USER_ID = 3;

	private LeadDialer $dialer;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::source(),
			LeadFixtureHelper::user(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::reports()
		);
	}

	public function testCallingNotAnswerLimit(): void {
		$this->giveDialer([
			'notAnsweredLimit' => 3,
		]);
		Lead::deleteAll();
		$lead = Module::manager()->pushLead(LeadFactory::createNewLead([
				'phone' => '123 123 123',
				'source_id' => 1,
				'name' => __METHOD__,
			]
		));
		$data = $this->dialer->calling();
		$this->tester->assertNotEmpty($data);
		$id = $data['id'];
		$this->tester->assertSame($id, $lead->getId());
		$this->dialer->notAnswer($id);
		$this->tester->assertNotEmpty($this->dialer->calling());
		$this->dialer->notAnswer($id);
		$this->tester->assertNotEmpty($this->dialer->calling());
		$this->dialer->notAnswer($id);
		$this->tester->assertEmpty($this->dialer->calling());
	}

	public function testCallingWithNotAnswer(): void {
		$this->giveDialer();
		$data = $this->dialer->calling();
		$this->tester->assertNotNull($data);
		$this->tester->assertArrayHasKey('id', $data);
		$this->tester->assertArrayHasKey('phone', $data);
		$id = $data['id'];
		$this->tester->seeRecord(Lead::class, [
			'id' => $id,
			'status_id' => $this->dialer->callingStatus,
		]);
		$this->tester->seeRecord(LeadReport::class, [
			'lead_id' => $id,
			'old_status_id' => LeadStatus::STATUS_NEW,
			'status_id' => $this->dialer->callingStatus,
			'owner_id' => $this->dialer->userId,
		]);

		$this->tester->assertTrue($this->dialer->notAnswer($id));
		$this->tester->seeRecord(Lead::class, [
			'id' => $id,
			'status_id' => $this->dialer->notAnsweredStatus,
		]);

		$this->tester->seeRecord(LeadReport::class, [
			'lead_id' => $id,
			'old_status_id' => $this->dialer->callingStatus,
			'status_id' => $this->dialer->notAnsweredStatus,
			'owner_id' => $this->dialer->userId,
		]);
	}

	public function testCallingWithAnswer(): void {
		$this->giveDialer();
		$data = $this->dialer->calling();
		$this->tester->assertNotNull($data);
		$this->tester->assertArrayHasKey('id', $data);
		$this->tester->assertArrayHasKey('phone', $data);
		$id = $data['id'];
		$this->tester->seeRecord(Lead::class, [
			'id' => $id,
			'status_id' => $this->dialer->callingStatus,
		]);
		$this->tester->seeRecord(LeadReport::class, [
			'lead_id' => $id,
			'old_status_id' => LeadStatus::STATUS_NEW,
			'status_id' => $this->dialer->callingStatus,
			'owner_id' => $this->dialer->userId,
		]);

		$this->tester->assertTrue($this->dialer->answer($id));
		$this->tester->seeRecord(Lead::class, [
			'id' => $id,
			'status_id' => $this->dialer->answeredStatus,
		]);

		$this->tester->seeRecord(LeadReport::class, [
			'lead_id' => $id,
			'old_status_id' => $this->dialer->callingStatus,
			'status_id' => $this->dialer->answeredStatus,
			'owner_id' => $this->dialer->userId,
		]);
	}

	protected function giveDialer(array $config = []): void {
		if (!isset($config['notAnsweredStatus'])) {
			$config['notAnsweredStatus'] = static::STATUS_NOT_ANSWERED;
		}
		if (!isset($config['answeredStatus'])) {
			$config['answeredStatus'] = static::STATUS_ANSWERED;
		}
		if (!isset($config['callingStatus'])) {
			$config['callingStatus'] = static::STATUS_CALLING;
		}
		if (!isset($config['userId'])) {
			$config['userId'] = static::USER_ID;
		}
		$this->dialer = new LeadDialer($config);
	}

}
