<?php

namespace common\tests\unit\lead\search;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\DuplicateLead;
use common\modules\lead\models\searches\DuplicateLeadSearch;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;
use yii\helpers\ArrayHelper;

class DuplicateLeadSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::reports()
		);
	}

	public function testDefault(): void {
		$this->model = $this->createModel();
		$models = $this->search()->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertInstanceOf(DuplicateLead::class, $model);
			$this->tester->assertNotEmpty($model->getSameContacts());
		}
	}

	public function testSameStatus(): void {
		$this->model = $this->createModel();
		$this->model->status = DuplicateLeadSearch::STATUS_SAME;
		$models = $this->search()->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertInstanceOf(DuplicateLead::class, $model);
			$this->tester->assertNotEmpty($model->getSameContacts());
			$sameStatuses = ArrayHelper::getColumn($model->getSameContacts(), 'status_id');
			$this->tester->assertContains($model->status_id, $sameStatuses);
		}
	}

	public function testVariousStatus(): void {
		$this->model = $this->createModel();
		$this->model->status = DuplicateLeadSearch::STATUS_VARIOUS;
		$models = $this->search()->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertInstanceOf(DuplicateLead::class, $model);
			$this->tester->assertNotEmpty($model->getSameContacts());
			$sameStatuses = ArrayHelper::getColumn($model->getSameContacts(), 'status_id');
			$modelStatus = $model->status_id;
			$notModelStatuses = array_filter($sameStatuses, static function (int $statusId) use ($modelStatus): bool {
				return $statusId !== $modelStatus;
			});
			$this->tester->assertNotEmpty($notModelStatuses);
		}
	}

	protected function createModel(): DuplicateLeadSearch {
		return new DuplicateLeadSearch();
	}
}
