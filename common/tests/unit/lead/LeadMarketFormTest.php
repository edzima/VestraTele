<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\fixtures\helpers\TerytFixtureHelper;
use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\forms\LeadMarketForm;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadReport;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

class LeadMarketFormTest extends Unit {

	use UnitModelTrait;

	private LeadMarketForm $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::address(),
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::market(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::user(),
			LeadFixtureHelper::reports(),
			LeadFixtureHelper::source(),
			TerytFixtureHelper::fixtures(),
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Lead Id cannot be blank.', 'lead_id');
		$this->thenSeeError('Status cannot be blank.', 'status');
		$this->thenSeeError('Creator Id cannot be blank.', 'creator_id');
	}

	public function testLeadWithSameContactAndType(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Lead Id cannot be blank.', 'lead_id');
		$this->thenSeeError('Status cannot be blank.', 'status');
		$this->thenSeeError('Creator Id cannot be blank.', 'creator_id');
	}

	public function testCreateForLeadFromFixture(): void {
		$this->giveModel([
			'status' => LeadMarket::STATUS_NEW,
			'lead_id' => 1,
			'details' => 'Duplicate Lead from Fixture',
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Lead Id "1" has already been taken.', 'lead_id');
	}

	public function testSaveNew(): void {
		$this->giveModel([
			'creator_id' => 1,
			'status' => LeadMarket::STATUS_NEW,
			'lead_id' => 2,
			'details' => 'New Market Test Lead',
			'options' => new LeadMarketOptions([
				'visibleArea' => LeadMarketOptions::VISIBLE_ADDRESS_REGION,
			]),
		]);

		$this->thenSuccessSave();

		$market = $this->grabMarket([
			'details' => 'New Market Test Lead',
		]);

		$this->tester->assertNotNull($market);
		$this->tester->assertSame(1, $market->creator_id);
		$this->tester->assertSame(2, $market->lead_id);
		$this->tester->assertSame(LeadMarket::STATUS_NEW, $market->status);
		$this->tester->assertSame(LeadMarketOptions::VISIBLE_ADDRESS_REGION, $market->getMarketOptions()->visibleArea);
	}

	public function testSaveWithoutAddress(): void {
		$this->giveModel([
			'status' => LeadMarket::STATUS_NEW,
			'lead_id' => 4,
			'details' => 'Lead without Address',
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Lead must have Address.', 'lead_id');
	}

	public function testReportWithoutSave(): void {
		$this->giveModel([
			'creator_id' => 1,
			'status' => LeadMarket::STATUS_NEW,
			'lead_id' => 2,
			'details' => 'New Market Test Lead',
		]);

		$this->tester->assertFalse($this->getModel()->saveReport());
	}

	public function testReportAfterSave(): void {
		$this->giveModel([
			'creator_id' => 1,
			'status' => LeadMarket::STATUS_NEW,
			'lead_id' => 2,
			'details' => 'New Market Test Lead',
		]);
		$this->thenSuccessSave();
		$this->tester->assertTrue($this->getModel()->saveReport());
		$this->tester->seeRecord(LeadReport::class, [
			'owner_id' => 1,
			'lead_id' => 2,
			'details' => 'Move Lead to Market',
		]);
	}

	public function testSameLeadAlreadyInMarket(): void {
		$this->giveModel([
			'creator_id' => 1,
			'status' => LeadMarket::STATUS_NEW,
			'lead_id' => 3,
			'details' => 'Market for Lead with same contacts in Market.',
		]);

		$this->thenUnsuccessSave();
		$this->thenSeeError('Same Lead has already in Market.', 'lead_id');
	}

	public function testUpdate(): void {
		$this->giveModel([
			'creator_id' => 1,
			'status' => LeadMarket::STATUS_NEW,
			'lead_id' => 2,
			'details' => 'Market Lead test for Update',
		]);

		$this->thenSuccessSave();

		$market = $this->model->getModel();

		$this->giveModel([
			'model' => $market,
		]);

		$this->model->details = 'updated details';

		$this->thenSuccessSave();
		$this->tester->seeRecord(LeadMarket::class, [
			'id' => $market->id,
			'details' => 'updated details',
		]);
	}

	private function giveModel(array $config = []): void {
		$this->model = new LeadMarketForm($config);
	}

	public function getModel(): LeadMarketForm {
		return $this->model;
	}

	public function grabMarket(array $attributes = []): ?LeadMarket {
		return $this->tester->grabRecord(LeadMarket::class, $attributes);
	}

}
