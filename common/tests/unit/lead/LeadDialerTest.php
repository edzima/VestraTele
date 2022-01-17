<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\components\LeadDialer;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadStatusInterface;
use common\tests\unit\Unit;

class LeadDialerTest extends Unit {

	private const STATUS_CALLING = 2;
	private const STATUS_NOT_ANSWERED = 3;
	private const STATUS_ANSWERED = 4;
	private const USER_ID = 3;
	private const NEXT_CALL_TRY_INTERVAL = 1;

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

	public function testNewCallingWithoutUserFlagEnable(): void {
		$this->giveDialer([
			'withNewWithoutUser' => true,
		]);
		$model = $this->dialer->findToCall();
		$this->tester->assertNotEmpty($model);
		$data = $this->dialer->calling();
		$this->tester->assertNotEmpty($data);
	}

	public function testNewCallingWithWithoutUserFlagDisable(): void {
		$this->giveDialer([
			'withNewWithoutUser' => false,
		]);
		$data = $this->dialer->calling();
		$this->tester->assertEmpty($data);
	}

	public function testCallingAndNotAnswer(): void {
		$this->giveDialer([
			'withNewWithoutUser' => true,
		]);
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
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
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

	public function testCallingAndAnswer(): void {
		$this->giveDialer([
			'withNewWithoutUser' => true,
		]);
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
			'old_status_id' => LeadStatusInterface::STATUS_NEW,
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

	public function testCallingTriesWithoutDayLimit(): void {
		$this->giveDialer([
			'withNewWithoutUser' => true,
		]);
		$this->dialer->callingTryDayLimit = null;
		$data = $this->dialer->calling();
		$this->tester->assertNotEmpty($data['id']);
		$id = $data['id'];
		$lead = $this->tester->grabRecord(Lead::class, ['id' => $id]);
		/** @var Lead $lead */
		$this->dialer->notAnswer($id);
		$i = 0;
		while ($i < 10) {
			$lead->refresh();
			$data = $this->dialer->calling($lead);
			$this->tester->assertSame($data['id'], $lead->getId());
			$this->dialer->notAnswer($id);
			$i++;
		}
	}

	public function testCallingTriesWithDayLimitWithoutInterval(): void {
		$this->giveDialer([
			'withNewWithoutUser' => true,
		]);
		$dayLimit = 1;
		$this->dialer->callingTryDayLimit = $dayLimit;
		$data = $this->dialer->calling();
		$this->tester->assertNotEmpty($data['id']);
		$id = $data['id'];
		$lead = $this->tester->grabRecord(Lead::class, ['id' => $id]);
		/** @var Lead $lead */
		$this->dialer->notAnswer($id);
		$this->tester->assertEmpty($this->dialer->calling($lead));
		$i = 1;
		while ($i < $dayLimit) {
			$lead->refresh();
			sleep(static::NEXT_CALL_TRY_INTERVAL + 1);
			$data = $this->dialer->calling($lead);
			$this->tester->assertSame($data['id'], $lead->getId());
			$this->dialer->notAnswer($id);
			$i++;
		}
		$this->tester->assertEmpty($this->dialer->calling($lead));
	}

	protected function grabLead(int $id): ?ActiveLead {
		return $this->tester->grabRecord(Lead::class, ['id' => $id]);
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
		if (!isset($config['nextCallInterval'])) {
			$config['nextCallInterval'] = static::NEXT_CALL_TRY_INTERVAL;
		}
		if (!isset($config['userId'])) {
			$config['userId'] = static::USER_ID;
		}
		$this->dialer = new LeadDialer($config);
	}

}
