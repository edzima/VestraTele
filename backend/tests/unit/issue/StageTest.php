<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueStage;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\issue\StageFixture;
use common\fixtures\issue\StageTypesFixtures;
use common\fixtures\issue\TypeFixture;

class StageTest extends Unit {

	protected function _before(): void {
		parent::_before();
		$this->tester->haveFixtures([
			'stage-types' => [
				'class' => StageTypesFixtures::class,
				'dataFile' => IssueFixtureHelper::dataDir() . 'issue/stage_types.php',

			],
			'stage' => [
				'class' => StageFixture::class,
				'dataFile' => IssueFixtureHelper::dataDir() . 'issue/stage.php',
			],
			'type' => [
				'class' => TypeFixture::class,
				'dataFile' => IssueFixtureHelper::dataDir() . 'issue/type.php',
			],
		]);
	}

	public function testDuplicateName(): void {
		$model = new IssueStage(['name' => 'Completing documents']);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name "Completing documents" has already been taken.', $model->getFirstError('name'));
	}

	public function testDuplicateShortName(): void {
		$model = new IssueStage(['name' => 'Completing', 'short_name' => 'CD']);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Shortname "CD" has already been taken.', $model->getFirstError('short_name'));
	}

	public function testEmpty(): void {
		$model = new IssueStage();
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name cannot be blank.', $model->getFirstError('name'));
		$this->tester->assertSame('Shortname cannot be blank.', $model->getFirstError('short_name'));
		$this->tester->assertSame('Types cannot be blank.', $model->getFirstError('typesIds'));
	}

	public function testEmptyTypes(): void {
		$model = new IssueStage([
			'name' => 'Some name',
			'short_name' => 'SN',
			'typesIds' => [
			],
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Types cannot be blank.', $model->getFirstError('typesIds'));
	}

	public function testInvalidTypes(): void {
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

	public function testCorrectCreate(): void {
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
