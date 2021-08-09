<?php

namespace common\tests\unit\calendar\searches;

use common\fixtures\helpers\LeadFixtureHelper;
use common\models\SearchModel;
use common\modules\calendar\models\searches\LeadCalendarSearch;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;
use yii\base\InvalidConfigException;

/**
 * @property LeadCalendarSearch $model
 */
class LeadSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::user(),
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::status(),
			LeadFixtureHelper::reminder()
		);
	}

	public function testWithoutUser(): void {
		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			$this->giveModel();
			$this->search();
		});
	}

	public function testFilters(): void {
		$this->giveModel();
		$filters = $this->model->getFilters();
		$this->tester->assertNotEmpty($filters);

		foreach ($filters as $filter) {
			$this->tester->assertContains(
				$filter->label, ['New', 'Not Answered']
			);
			$this->tester->assertNotContains(
				$filter->label, ['Archive']
			);
		}
	}

	private function giveModel(): void {
		$this->model = $this->createModel();
	}

	protected function createModel(): SearchModel {
		return new LeadCalendarSearch();
	}
}
