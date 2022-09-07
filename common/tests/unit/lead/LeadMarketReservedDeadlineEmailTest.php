<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadMarketReservedDeadlineEmail;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\tests\unit\Unit;
use yii\mail\MessageInterface;

class LeadMarketReservedDeadlineEmailTest extends Unit {

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::market(),
			LeadFixtureHelper::user()
		);
	}

	public function _before() {
		parent::_before();
		LeadMarket::deleteAll();
	}

	public function testEmpty(): void {
		$model = new LeadMarketReservedDeadlineEmail();
		$this->tester->assertFalse($model->validate());
		$this->tester->assertNull($model->sendEmails());
		$this->tester->assertSame('Days cannot be blank.', $model->getFirstError('days'));
	}

	public function testAsOneDay(): void {
		$model = new LeadMarketReservedDeadlineEmail();
		$model->days = 1;
		$this->tester->assertNull($model->sendEmails());

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_BOOKED),
			'user_id' => 1,
			'reserved_at' => date('Y-m-d', strtotime('+ 1 day')),
		]);

		$this->tester->assertSame(1, $model->sendEmails());

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_BOOKED),
			'user_id' => 1,
			'reserved_at' => date('Y-m-d', strtotime('+ 2 days')),
		]);

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_BOOKED),
			'user_id' => 1,
			'reserved_at' => date('Y-m-d', strtotime('+ 3 days')),
		]);

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_ARCHIVED),
			'user_id' => 2,
			'reserved_at' => date('Y-m-d', strtotime('+ 1 day')),
		]);

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_BOOKED),
			'user_id' => 2,
			'reserved_at' => date('Y-m-d', strtotime('+ 1 day')),
		]);

		$this->tester->assertSame(2, $model->sendEmails());
		$this->tester->seeEmailIsSent();
		/** @var MessageInterface $email */
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('Tomorrow you will lose access to the Lead from Market.', $email->getSubject());
	}

	public function testAsZero(): void {
		$model = new LeadMarketReservedDeadlineEmail();
		$model->days = 0;
		$this->tester->assertNull($model->sendEmails());

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_BOOKED),
			'user_id' => 1,
			'reserved_at' => date('Y-m-d'),
		]);

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_ARCHIVED),
			'user_id' => 2,
			'reserved_at' => date('Y-m-d'),
		]);

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_AVAILABLE_AGAIN),
			'user_id' => 2,
			'reserved_at' => date('Y-m-d'),
		]);
		$this->tester->assertSame(1, $model->sendEmails());
		$this->tester->seeEmailIsSent(1);
		/** @var MessageInterface $email */
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('Today you will lose access to the Lead from Market.', $email->getSubject());
	}

	public function testLessThenZero(): void {
		$model = new LeadMarketReservedDeadlineEmail();
		$model->days = -1;
		$this->tester->assertNull($model->sendEmails());

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_BOOKED),
			'user_id' => 1,
			'reserved_at' => date('Y-m-d', strtotime('- 1 day')),
		]);

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_ARCHIVED),
			'user_id' => 2,
			'reserved_at' => date('Y-m-d'),
		]);

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_AVAILABLE_AGAIN),
			'user_id' => 2,
			'reserved_at' => date('Y-m-d', strtotime('- 1 day')),
		]);
		$this->tester->assertNull($model->sendEmails());
	}

	public function testGreaterThanOne(): void {
		$model = new LeadMarketReservedDeadlineEmail();
		$model->days = 2;
		$this->tester->assertNull($model->sendEmails());

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_BOOKED),
			'user_id' => 1,
			'reserved_at' => date('Y-m-d', strtotime('+ 2 days')),
		]);
		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_BOOKED),
			'user_id' => 1,
			'reserved_at' => date('Y-m-d', strtotime('+ 3 days')),
		]);

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_ARCHIVED),
			'user_id' => 2,
			'reserved_at' => date('Y-m-d', strtotime('+ 2 days')),
		]);

		$this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $this->haveMarket(LeadMarket::STATUS_BOOKED),
			'user_id' => 2,
			'reserved_at' => date('Y-m-d', strtotime('+ 2 days')),
		]);

		$this->tester->assertSame(2, $model->sendEmails());

		/** @var MessageInterface $email */
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('In 2 days you will lose access to the Lead from Market.', $email->getSubject());
	}

	private function haveMarket(int $status): int {
		return $this->tester->haveRecord(LeadMarket::class, [
			'status' => $status,
			'creator_id' => 1,
			'lead_id' => 1,
		]);
	}
}
