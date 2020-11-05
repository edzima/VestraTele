<?php

namespace backend\tests\unit\issue;

use common\fixtures\issue\EntityResponsibleFixture;
use common\models\entityResponsible\EntityResponsible;

class EntityResponsibleTest extends \Codeception\Test\Unit {

	/**
	 * @var \backend\tests\UnitTester
	 */
	protected $tester;

	protected function _before() {
		$this->tester->haveFixtures([
			'entity' => [
				'class' => EntityResponsibleFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/entity_responsible.php',
			],
		]);
	}

	public function testDuplicateName() {
		$model = new EntityResponsible(['name' => 'Alianz']);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name "Alianz" has already been taken.', $model->getFirstError('name'));
	}

	public function testValidCreate(): void {
		$model = new EntityResponsible(['name' => 'MOPS']);
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(EntityResponsible::class, [
			'name' => 'MOPS',
		]);
	}
}
