<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\components\MarketManager;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\tests\unit\Unit;
use yii\base\InvalidArgumentException;

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
				LeadFixtureHelper::user()
			);
	}

	public function testRenewExpired(): void {
		$this->tester->assertNull($this->manager->expiredRenew());

		$marketDone = $this->haveMarket([
			'status' => LeadMarket::STATUS_DONE,
		]);

		$marketBooked1 = $this->haveMarket([
			'status' => LeadMarket::STATUS_BOOKED,
		]);

		$marketBooked2 = $this->haveMarket([
			'status' => LeadMarket::STATUS_BOOKED,
		]);

		$this->tester->assertNull($this->manager->expiredRenew());

		$this->haveMarketUser($marketDone, 2, date(DATE_ATOM, strtotime('- 1 day')));
		$this->haveMarketUser($marketBooked1, 2, date(DATE_ATOM, strtotime('- 1 day')));
		$this->haveMarketUser($marketBooked2, 2, date(DATE_ATOM, strtotime('+ 1 day')));

		$this->tester->assertSame(1, $this->manager->expiredRenew());

		$this->tester->seeRecord(LeadMarket::class, [
			'id' => $marketBooked1,
			'status' => LeadMarket::STATUS_AVAILABLE_AGAIN,
		]);

		$this->tester->dontSeeRecord(LeadMarket::class, [
			'id' => $marketBooked2,
			'status' => LeadMarket::STATUS_AVAILABLE_AGAIN,
		]);

		$marketBookedMultiple = $this->haveMarket([
			'status' => LeadMarket::STATUS_BOOKED,
		]);

		$this->haveMarketUser($marketBookedMultiple, 2, date(DATE_ATOM, strtotime('- 1 day')));
		$this->haveMarketUser($marketBookedMultiple, 3, date(DATE_ATOM, strtotime('+ 1 day')));
		$this->tester->assertNull($this->manager->expiredRenew());

		$this->tester->dontSeeRecord(LeadMarket::class, [
			'id' => $marketBookedMultiple,
			'status' => LeadMarket::STATUS_AVAILABLE_AGAIN,
		]);
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

	private function haveMarketUser(int $marketId, int $userId, string $reserved_at = null, int $status = LeadMarketUser::STATUS_ACCEPTED) {
		if ($status === LeadMarketUser::STATUS_ACCEPTED && $reserved_at === null) {
			throw new InvalidArgumentException('Reserved At must be set on Status Booked.');
		}
		return $this->tester->haveRecord(LeadMarketUser::class, [
			'market_id' => $marketId,
			'user_id' => $userId,
			'status' => $status,
			'reserved_at' => $reserved_at,
		]);
	}
}
