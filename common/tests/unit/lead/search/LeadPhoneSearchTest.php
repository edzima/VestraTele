<?php

namespace common\tests\unit\lead\search;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\searches\LeadPhoneSearch;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;

class LeadPhoneSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before() {
		parent::_before();
		$this->model = $this->createModel();
	}

	public function _fixtures(): array {
		return LeadFixtureHelper::lead();
	}

	public function testEmpty(): void {
		$model = $this->model;
		$this->tester->assertFalse($model->validate());
		$this->tester->assertSame('Phone cannot be blank.', $model->getFirstError('phone'));
	}

	public function testDoublePhoneLead(): void {
		$models = $this->search(['phone' => '777-222-122'])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertStringContainsString('777-222-122', $model->phone);
		}
	}

	protected function createModel(): LeadPhoneSearch {
		return new LeadPhoneSearch();
	}

}
