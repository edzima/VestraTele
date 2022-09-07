<?php

namespace backend\tests\unit\issue;

use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssueType;

class TypeTest extends Unit {

	public function _fixtures(): array {
		return IssueFixtureHelper::types();
	}

	public function testDuplicateName(): void {
		$model = new IssueType(['name' => 'Accident']);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name "Accident" has already been taken.', $model->getFirstError('name'));
	}

	public function testDuplicateShortName(): void {
		$model = new IssueType(['name' => 'Accident2', 'short_name' => 'ACC']);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Shortname "ACC" has already been taken.', $model->getFirstError('short_name'));
	}

	public function testEmpty(): void {
		$model = new IssueType([
			'name' => '',
			'short_name' => '',
			'provision_type' => '',
			'vat' => '',
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name cannot be blank.', $model->getFirstError('name'));
		$this->tester->assertSame('Shortname cannot be blank.', $model->getFirstError('short_name'));
		$this->tester->assertSame('VAT (%) cannot be blank.', $model->getFirstError('vat'));
	}

	public function testValidCreate(): void {
		$model = new IssueType([
			'name' => 'Some name',
			'short_name' => 'SN',
			'vat' => 23,
		]);
		$model->validate();
		codecept_debug($model->getErrors());
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(IssueType::class, [
			'name' => 'Some name',
			'short_name' => 'SN',
			'vat' => 23,
		]);
	}

}
