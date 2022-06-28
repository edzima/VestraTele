<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadMarketMultipleForm;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadReport;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

class LeadMarketMultipleFormTest extends Unit {

	use UnitModelTrait;

	private LeadMarketMultipleForm $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::user(),
			LeadFixtureHelper::market(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::reports(),
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Creator Id cannot be blank.', 'creator_id');

		$this->thenSeeError('Leads Ids cannot be blank.', 'leadsIds');
		$this->thenSeeError('Status cannot be blank.', 'status');
	}

	public function testValid(): void {
		$this->giveModel([
			'status' => LeadMarket::STATUS_NEW,
			'creator_id' => 1,
			'details' => 'Valid Multiple Details',
		]);
		$this->model->leadsIds = [1, 2, 3];

		$saveCount = $this->model->save();
		$this->tester->assertSame(1, $saveCount);

		$this->tester->wantTo('Check Filter Lead with Market from Fixture');
		$this->tester->dontSeeRecord(LeadMarket::class, [
			'status' => LeadMarket::STATUS_NEW,
			'details' => 'Valid Multiple Details',
			'lead_id' => 1,
		]);

		$this->tester->wantTo('Check Filter Lead with has Same Contact in Market from Fixture');
		$this->tester->dontSeeRecord(LeadMarket::class, [
			'status' => LeadMarket::STATUS_NEW,
			'details' => 'Valid Multiple Details',
			'lead_id' => 2,
		]);

		$this->tester->seeRecord(LeadMarket::class, [
			'status' => LeadMarket::STATUS_NEW,
			'details' => 'Valid Multiple Details',
			'lead_id' => 3,
		]);

		$this->tester->assertSame(1, $this->model->saveReports(false));

		$this->tester->dontSeeRecord(LeadReport::class, [
			'details' => 'Move Lead to Market',
			'lead_id' => 1,
		]);
		$this->tester->dontSeeRecord(LeadReport::class, [
			'details' => 'Move Lead to Market',
			'lead_id' => 2,
		]);

		$this->tester->seeRecord(LeadReport::class, [
			'details' => 'Move Lead to Market',
			'lead_id' => 3,
		]);
	}

	private function giveModel(array $config = []): void {
		$this->model = new LeadMarketMultipleForm($config);
	}

	public function getModel(): LeadMarketMultipleForm {
		return $this->model;
	}

	public function grabMarket(array $attributes = []): ?LeadMarket {
		return $this->tester->grabRecord(LeadMarket::class, $attributes);
	}

}
