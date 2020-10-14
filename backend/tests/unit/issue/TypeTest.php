<?php namespace backend\tests\issue;

use common\fixtures\issue\TypeFixture;
use common\models\issue\IssueType;
use common\models\issue\Provision;

class TypeTest extends \Codeception\Test\Unit {

	/**
	 * @var \backend\tests\UnitTester
	 */
	protected $tester;

	protected function _before() {
		$this->tester->haveFixtures([
			'type' => [
				'class' => TypeFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/type.php',
			],
		]);
	}

	public function testDuplicateName() {
		$model = new IssueType(['name' => 'Accident']);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name "Accident" has already been taken.', $model->getFirstError('name'));
	}

	public function testDuplicateShortName() {
		$model = new IssueType(['name' => 'Accident2', 'short_name' => 'ACC']);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Shortname "ACC" has already been taken.', $model->getFirstError('short_name'));
	}

	public function testInvalidProvisionType() {
		$model = new IssueType([
			'name' => 'Benefits',
			'short_name' => 'B',
			'provision_type' => 10000,
			'vat' => 23,
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Provision type is invalid.', $model->getFirstError('provision_type'));
	}

	public function testEmpty() {
		$model = new IssueType([
			'name' => '',
			'short_name' => '',
			'provision_type' => '',
			'vat' => '',
		]);
		$this->tester->assertFalse($model->save());
		$this->tester->assertSame('Name cannot be blank.', $model->getFirstError('name'));
		$this->tester->assertSame('Shortname cannot be blank.', $model->getFirstError('short_name'));
		$this->tester->assertSame('Provision type cannot be blank.', $model->getFirstError('provision_type'));
		$this->tester->assertSame('VAT (%) cannot be blank.', $model->getFirstError('vat'));
	}

	public function testValidCreate(): void {
		$model = new IssueType([
			'name' => 'Some name',
			'short_name' => 'SN',
			'provision_type' => Provision::TYPE_PERCENTAGE,
			'vat' => 23,
		]);
		$this->tester->assertTrue($model->save());
		$this->tester->seeRecord(IssueType::class, [
			'name' => 'Some name',
			'short_name' => 'SN',
			'provision_type' => Provision::TYPE_PERCENTAGE,
			'vat' => 23,
		]);
	}

}
