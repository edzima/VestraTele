<?php

namespace backend\tests\unit\settlement;

use backend\modules\settlement\models\CalculationMinCountForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\StageType;

class CalculationMinCountFormTest extends Unit {

	public function _before():void {
		parent::_before();
		$this->tester->haveFixtures(IssueFixtureHelper::stageAndTypesFixtures());
	}

	public function testNotExistedStage(): void {
		$model = new CalculationMinCountForm();
		$model->typeId = 1;
		$model->stageId = 100;
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Stage is invalid.', $model->getFirstError('stageId'));
	}

	public function testNotExistedType(): void {
		$model = new CalculationMinCountForm();
		$model->typeId = 100;
		$model->stageId = 1;
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Type is invalid.', $model->getFirstError('typeId'));
	}

	public function testCorrect(): void {
		$model = new CalculationMinCountForm();
		$model->typeId = 1;
		$model->stageId = 1;
		$model->minCount = 1;
		$this->tester->assertTrue($model->save());

		$this->tester->seeRecord(StageType::class, [
			'stage_id' => 1,
			'type_id' => 1,
			'min_calculation_count' => 1,
		]);
	}

	public function testCountAsZeroFor(): void {
		$model = new CalculationMinCountForm();
		$model->typeId = 1;
		$model->stageId = 1;
		$model->minCount = 0;
		$model->validate();
		$this->tester->assertTrue($model->save());

		$this->tester->seeRecord(StageType::class, [
			'stage_id' => 1,
			'type_id' => 1,
			'min_calculation_count' => null,
		]);
	}

}
