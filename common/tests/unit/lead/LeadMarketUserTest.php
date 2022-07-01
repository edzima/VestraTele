<?php

namespace common\tests\unit\lead;

use common\modules\lead\models\LeadMarketUser;
use common\tests\unit\Unit;

class LeadMarketUserTest extends Unit {

	public function testGenerateReservedAt(): void {
		$model = new LeadMarketUser();
		$model->days_reservation = 2;
		$model->generateReservedAt(strtotime('2020-01-01'));
		$this->assertSame('2020-01-03', $model->reserved_at);
	}

	public function testGenerateReservedAtForEmptyDays(): void {
		$model = new LeadMarketUser();
		$model->days_reservation = null;
		$model->generateReservedAt();
		$this->assertNull($model->reserved_at);
	}

	public function testAccepted(): void {
		$model = new LeadMarketUser();
		$model->days_reservation = 2;
		$model->accept();
		$this->tester->assertSame(LeadMarketUser::STATUS_ACCEPTED, $model->status);
		$this->tester->assertSame($this->generateReservedAt(2), $model->reserved_at);
	}

	public function testRejected(): void {
		$model = new LeadMarketUser();
		$model->reserved_at = '2020-02-02';
		$model->reject();
		$this->tester->assertSame(LeadMarketUser::STATUS_REJECTED, $model->status);
		$this->assertNull($model->reserved_at);
	}

	private function generateReservedAt(int $days): string {
		return date('Y-m-d', strtotime("+ $days days"));
	}

}
