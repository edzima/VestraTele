<?php

namespace common\tests\unit\lead\dialer;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\components\DialerManager;
use common\modules\lead\entities\LeadDialerEntity;
use common\modules\lead\models\LeadDialer;
use common\tests\unit\Unit;

class LeadManagerTest extends Unit {

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

	public function testOrder(): void {
		/** @var LeadDialer[] $models */
		$models = $this->manager->toCallQuery()->all();
		$first = $models[0];
		$this->tester->assertSame(2, $first->id);
		$this->tester->assertSame(LeadDialer::PRIORITY_HIGH, $first->priority);
		$second = $models[1];
		$this->tester->assertSame(5, $second->id);
		$this->tester->assertSame(LeadDialer::PRIORITY_MEDIUM, $second->priority);
	}

	public function testCall(): void {
		$dialer = $this->manager->findToCall();
		$this->tester->assertNotNull($dialer);
		$this->tester->assertTrue($this->manager->calling($dialer));
		$this->tester->seeRecord(LeadDialer::class, [
			'id' => $dialer->getID(),
			'status_id' => LeadDialerEntity::STATUS_CALLING,
		]);
	}
}
