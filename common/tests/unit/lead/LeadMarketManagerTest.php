<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\components\MarketManager;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\LeadUser;
use common\tests\unit\Unit;
use yii\base\InvalidArgumentException;
use yii\mail\MessageInterface;

class LeadMarketManagerTest extends Unit {

	private MarketManager $manager;

	public function _before() {
		$this->manager = new MarketManager();
		parent::_before();
	}

	public function _fixtures(): array {
		return
			array_merge(
				LeadFixtureHelper::market(),
				LeadFixtureHelper::lead(),
				LeadFixtureHelper::user(),
				LeadFixtureHelper::status(),
			);
	}

	public function testFirstWaitingUser(): void {
		$marketId = $this->haveMarket();

		$this->haveMarketUser($marketId, 1, null, LeadMarketUser::STATUS_TO_CONFIRM);
		sleep(0.5);
		$this->haveMarketUser($marketId, 2, null, LeadMarketUser::STATUS_WAITING);
		sleep(0.5);
		$this->haveMarketUser($marketId, 3, null, LeadMarketUser::STATUS_WAITING);

		$first = $this->manager->getFirstWaitingUser($this->tester->grabRecord(LeadMarket::class, [
			'id' => $marketId,
		]));

		$this->tester->assertSame($first->user_id, 2);

		$updatedBefore = $this->tester->grabRecord(LeadMarketUser::class, [
			'user_id' => 1,
			'market_id' => $marketId,
		]);
		$updatedBefore->status = LeadMarketUser::STATUS_WAITING;
		$first = $this->manager->getFirstWaitingUser($this->tester->grabRecord(LeadMarket::class, [
			'id' => $marketId,
		]));
		$this->tester->assertSame($first->user_id, 2);
	}

	public function testEmailLeadStatus(): void {
		$this->tester->assertTrue($this->manager->sendLeadChangeStatusEmail($this->tester->grabFixture(LeadFixtureHelper::MARKET, 0)));
		$this->tester->seeEmailIsSent();
		/** @var MessageInterface $mail */
		$mail = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('Lead: John from Market change Status: New.', $mail->getSubject());
	}

	public function testExpireProcessForNotBooked(): void {
		$statuses = array_keys(LeadMarket::getStatusesNames());
		unset($statuses[LeadMarket::STATUS_BOOKED]);

		$count = $this->manager->expireProcess($this->grabMarket([
			'id' => $this->haveMarket([
				'status' => array_rand($statuses),
			]),
		]));
		$this->tester->assertNull($count);
	}

	public function testExpireProcessForBookedWithoutUsers(): void {
		$count = $this->manager->expireProcess($this->grabMarket([
			'id' => $this->haveMarket([
				'status' => LeadMarket::STATUS_BOOKED,
			]),
		]));
		$this->tester->assertNull($count);
	}

	public function testExpireProcessWithNotExpiredUser(): void {
		$marketId = $this->haveMarket([
			'status' => LeadMarket::STATUS_BOOKED,
		]);

		$this->haveAcceptedUser(
			$marketId,
			1,
			date(DATE_ATOM, strtotime('+ 1 day'))
		);

		$count = $this->manager->expireProcess(
			$this->grabMarket([
				'id' => $marketId,
			])
		);
		$this->tester->assertSame(0, $count);
	}

	public function testExpireProcessWithExpiredUserWithoutWaiting(): void {
		$marketId = $this->haveMarket([
			'status' => LeadMarket::STATUS_BOOKED,
		]);

		$this->haveAcceptedUser(
			$marketId,
			1,
			date(DATE_ATOM, strtotime('- 1 day'))
		);

		$this->haveAcceptedUser(
			$marketId,
			2,
			date(DATE_ATOM, strtotime('+ 1 day'))
		);

		$count = $this->manager->expireProcess(
			$this->grabMarket([
				'id' => $marketId,
			])
		);
		$this->tester->assertSame(1, $count);
	}

	public function testExpireProcessWithExpiredUserWithDoubleWaiting(): void {
		LeadUser::deleteAll();
		$marketId = $this->haveMarket([
			'status' => LeadMarket::STATUS_BOOKED,
		]);

		$this->haveAcceptedUser(
			$marketId,
			1,
			date(DATE_ATOM, strtotime('- 1 day'))
		);

		$this->haveMarketUser(
			$marketId,
			2,
			null,
			LeadMarketUser::STATUS_WAITING,
		);

		$this->haveMarketUser(
			$marketId,
			3,
			null,
			LeadMarketUser::STATUS_WAITING,
		);

		$count = $this->manager->expireProcess(
			$this->grabMarket([
				'id' => $marketId,
			])
		);
		$this->tester->assertSame(1, $count);
		$this->tester->seeEmailIsSent(1);
		/**
		 * @var MessageInterface $mail
		 */
		$mail = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('Your Access Request is Accepted.', $mail->getSubject());
	}

	public function testExpireProcessWithExpiredUserAndToConfirms(): void {
		$marketId = $this->haveMarket([
			'status' => LeadMarket::STATUS_BOOKED,
		]);

		$this->haveAcceptedUser(
			$marketId,
			1,
			date(DATE_ATOM, strtotime('- 1 day'))
		);

		$this->haveMarketUser(
			$marketId,
			2,
			null,
			LeadMarketUser::STATUS_TO_CONFIRM,
		);

		$this->haveMarketUser(
			$marketId,
			3,
			null,
			LeadMarketUser::STATUS_TO_CONFIRM,
		);
		$market = $this->grabMarket([
			'id' => $marketId,
		]);
		$count = $this->manager->expireProcess($market);
		$this->tester->assertSame(1, $count);
		$this->tester->seeEmailIsSent(1);
		$this->tester->seeEmailIsSent(1);
		/**
		 * @var MessageInterface $mail
		 */
		$mail = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('2 Access Request to Confirm for Lead: ' . $market->lead->getName() . ' from Market.', $mail->getSubject());
	}

	public function testMarketHasExpiredReservation(): void {
		/** @var LeadMarket $market */
		$market = $this->tester->grabRecord(LeadMarket::class, ['id' => $this->haveMarket()]);

		$this->tester->assertNull($market->hasActiveReservation());

		$this->haveMarketUser($market->id, 1, null, LeadMarketUser::STATUS_TO_CONFIRM);
		$market->refresh();
		$this->tester->assertFalse($market->hasActiveReservation());

		$this->haveMarketUser($market->id, 2, date(DATE_ATOM, strtotime(' + 1 day')), LeadMarketUser::STATUS_ACCEPTED);
		$market->refresh();
		$this->tester->assertTrue($market->hasActiveReservation());
	}

	private function haveMarket(array $attributes = []): int {
		if (!isset($attributes['lead_id'])) {
			$attributes['lead_id'] = 1;
		}
		if (!isset($attributes['creator_id'])) {
			$attributes['creator_id'] = 1;
		}
		return $this->tester->haveRecord(LeadMarket::class, $attributes);
	}

	private function haveAcceptedUser(int $marketId, int $userId, string $reservedAt): array {
		return $this->haveMarketUser($marketId, $userId, $reservedAt, LeadMarketUser::STATUS_ACCEPTED);
	}

	private function haveMarketUser(int $marketId, int $userId, string $reserved_at = null, int $status = LeadMarketUser::STATUS_ACCEPTED): array {
		if ($status === LeadMarketUser::STATUS_ACCEPTED && $reserved_at === null) {
			throw new InvalidArgumentException('Reserved At must be set on Status Booked . ');
		}
		return $this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $marketId,
			'user_id' => $userId,
			'status' => $status,
			'reserved_at' => $reserved_at,
		]);
	}

	private function grabMarket(array $attributes): LeadMarket {
		return $this->tester->grabRecord(LeadMarket::class, $attributes);
	}
}
