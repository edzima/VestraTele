<?php

namespace common\tests\unit\lead\dialer;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\components\DialerManager;
use common\modules\lead\entities\Dialer;
use common\modules\lead\entities\LeadDialerEntity;
use common\modules\lead\models\LeadDialer;
use common\tests\unit\Unit;

class LeadDialerManagerTest extends Unit {

	protected const DEAFULT_USER_ID = 1;

	private DialerManager $manager;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports(),
			LeadFixtureHelper::dialer()
		);
	}

	public function _before() {
		parent::_before();
		$this->manager = new DialerManager([
			'userId' => static::DEAFULT_USER_ID,
		]);
	}

	public function testCall(): void {
		$dialer1 = $this->manager->findToCall();
		$this->tester->assertNotNull($dialer1);
		$this->tester->assertTrue($this->manager->calling($dialer1));
		$this->tester->assertFalse($this->manager->calling($dialer1));
		$this->tester->seeRecord(LeadDialer::class, [
			'id' => $dialer1->getID(),
			'status' => LeadDialerEntity::STATUS_CALLING,
		]);

		$dialer2 = $this->manager->findToCall();
		$this->tester->assertNotNull($dialer2);
		$this->tester->assertNotSame($dialer1->getID(), $dialer2->getID());
		$this->tester->assertTrue($this->manager->calling($dialer2));
		$this->tester->seeRecord(LeadDialer::class, [
			'id' => $dialer2->getID(),
			'status' => Dialer::STATUS_CALLING,
		]);

		$dialer3 = $this->manager->findToCall();
		$this->tester->assertNull($dialer3);
	}

	public function testCallAndEstablish(): void {
		$dialer = $this->manager->findToCall();
		$this->manager->calling($dialer);

		$this->tester->assertTrue($this->manager->establish($dialer));

		$this->tester->seeRecord(LeadDialer::class, [
			'id' => $dialer->getID(),
			'status' => Dialer::STATUS_ESTABLISHED,
		]);
	}

	public function testEstablishWithoutCall(): void {
		$dialer = $this->manager->findToCall();

		$this->tester->assertFalse($this->manager->establish($dialer));

		$this->tester->dontSeeRecord(LeadDialer::class, [
			'id' => $dialer->getID(),
			'status' => Dialer::STATUS_ESTABLISHED,
		]);
	}

	public function testCallAndNotEstablish(): void {
		$dialer = $this->manager->findToCall();
		$this->manager->calling($dialer);

		$this->tester->assertTrue($this->manager->notEstablish($dialer));

		$this->tester->seeRecord(LeadDialer::class, [
			'id' => $dialer->getID(),
			'status' => Dialer::STATUS_UNESTABLISHED,
		]);
	}

	public function testNotEstablishWithoutCall(): void {
		$dialer = $this->manager->findToCall();

		$this->tester->assertFalse($this->manager->notEstablish($dialer));

		$this->tester->dontSeeRecord(LeadDialer::class, [
			'id' => $dialer->getID(),
			'status' => Dialer::STATUS_UNESTABLISHED,
		]);
	}
}
