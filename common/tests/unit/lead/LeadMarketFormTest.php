<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\forms\LeadMarketForm;
use common\modules\lead\models\LeadMarket;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

class LeadMarketFormTest extends Unit {

	use UnitModelTrait;

	private LeadMarketForm $model;

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
		$this->thenSeeError('Lead Id cannot be blank.', 'lead_id');
		$this->thenSeeError('Status cannot be blank.', 'status');
	}

	public function testSaveNew(): void {
		$this->giveModel([
			'status' => LeadMarket::STATUS_NEW,
			'lead_id' => 1,
			'details' => 'New Market Test Lead',
			'options' => new LeadMarketOptions([
				'visibleRegion' => true,
			]),
		]);

		$this->thenSuccessSave();

		$market = $this->grabMarket([
			'details' => 'New Market Test Lead',
		]);

		$this->tester->assertNotNull($market);
		$this->tester->assertSame(1, $market->lead_id);
		$this->tester->assertSame(LeadMarket::STATUS_NEW, $market->status);
		$this->tester->assertTrue($market->getMarketOptions()->visibleRegion);
	}

	public function testUpdate(): void {
		$this->giveModel([
			'status' => LeadMarket::STATUS_NEW,
			'lead_id' => 1,
			'details' => 'New Market Test Lead',
			'options' => new LeadMarketOptions([
				'visibleRegion' => true,
			]),
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
