<?php

namespace backend\tests\issue;

use backend\modules\issue\models\IssueStage;
use common\fixtures\issue\StageFixture;
use common\fixtures\issue\StageTypesFixtures;
use common\fixtures\issue\TypeFixture;

class StageTest extends \Codeception\Test\Unit {

	/**
	 * @var \backend\tests\UnitTester
	 */
	protected $tester;

	protected function _before() {
		$this->tester->haveFixtures([
			'stage-types' => [
				'class' => StageTypesFixtures::class,
				'dataFile' => codecept_data_dir() . 'issue/stage_types.php',

			],
			'stage' => [
				'class' => StageFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/stage.php',
			],
			'type' => [
				'class' => TypeFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/type.php',
			],
		]);
	}

	public function testDuplicateName() {
		$model = new IssueStage(['name' => 'Completing documents']);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name "Completing documents" has already been taken.', $model->getFirstError('name'));
	}

	public function testDuplicateShortName() {
		$model = new IssueStage(['name' => 'Completing', 'short_name' => 'CD']);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Shortname "CD" has already been taken.', $model->getFirstError('short_name'));
	}

	public function testEmpty() {
		$model = new IssueStage();
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name cannot be blank.', $model->getFirstError('name'));
		$this->tester->assertSame('Shortname cannot be blank.', $model->getFirstError('short_name'));
		$this->tester->assertSame('Types cannot be blank.', $model->getFirstError('typesIds'));
	}

	public function testEmptyTypes() {
		$model = new IssueStage([
			'name' => 'Some name',
			'short_name' => 'SN',
			'typesIds' => [
			],
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Types cannot be blank.', $model->getFirstError('typesIds'));
	}

	public function testInvalidTypes() {
		$model = new IssueStage([
			'name' => 'Some name',
			'short_name' => 'SN',
			'typesIds' => [
				100, 200,
			],
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Types is invalid.', $model->getFirstError('typesIds'));
	}

	public function testCorrectCreate() {
		$model = new IssueStage([
			'name' => 'Some name',
			'short_name' => 'SN',
			'typesIds' => [
				1, 2,
			],
		]);
		$this->tester->assertTrue($model->save());
		/** @var IssueStage $stage */
		$stage = $this->tester->grabRecord(IssueStage::class, [
			'name' => 'Some name',
			'short_name' => 'SN',
		]);
		$this->assertSame('Accident, Benefits - administrative proceedings', $stage->getTypesName());
		$this->assertSame([1, 2], $stage->typesIds);
	}

}
