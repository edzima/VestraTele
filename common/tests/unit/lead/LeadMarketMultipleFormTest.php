<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadMarketMultipleForm;
use common\modules\lead\models\LeadMarket;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

class LeadMarketMultipleFormTest extends Unit {

	use UnitModelTrait;

	private LeadMarketMultipleForm $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::user(),
			LeadFixtureHelper::market()
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Leads Ids cannot be blank.', 'leadsIds');
		$this->thenSeeError('Status cannot be blank.', 'status');
	}

	public function testValid(): void {
		$this->giveModel([
			'status' => LeadMarket::STATUS_NEW,
			'details' => 'Valid Multiple Details',
		]);
		$this->model->leadsIds = [1, 2];

		$saveCount = $this->model->save();
		$this->tester->assertSame(2, $saveCount);
		$this->tester->seeRecord(LeadMarket::class, [
			'status' => LeadMarket::STATUS_NEW,
			'details' => 'Valid Multiple Details',
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
