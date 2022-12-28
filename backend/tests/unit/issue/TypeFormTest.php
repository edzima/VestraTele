<?php

namespace backend\tests\unit\issue;

use backend\modules\issue\models\IssueTypeForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\IssueType;
use common\tests\_support\UnitModelTrait;

class TypeFormTest extends Unit {

	use UnitModelTrait;

	private IssueTypeForm $model;

	public function _fixtures(): array {
		return IssueFixtureHelper::types();
	}

	public function testDuplicateName(): void {
		$this->giveModel(['name' => 'Accident']);
		$this->thenUnsuccessSave();
		$this->thenSeeError('Name "Accident" has already been taken.', 'name');
	}

	private function giveModel(array $config = []): void {
		$this->model = new IssueTypeForm($config);
	}

	public function testDuplicateShortName(): void {
		$this->giveModel(['name' => 'Accident2', 'short_name' => 'ACC']);
		$this->thenUnsuccessSave();
		$this->thenSeeError('Shortname "ACC" has already been taken.', 'short_name');
	}

	public function testEmpty(): void {
		$this->giveModel([
			'name' => '',
			'short_name' => '',
			'vat' => '',
		]);
		$this->thenUnsuccessSave();
		$this->thenSeeError('Name cannot be blank.', 'name');
		$this->thenSeeError('Shortname cannot be blank.', 'short_name');
		$this->thenSeeError('VAT (%) cannot be blank.', 'vat');
	}

	public function testCreateWithoutParent(): void {
		$this->giveModel([
			'name' => 'Some name',
			'short_name' => 'SN',
			'vat' => 23,
		]);
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssueType::class, [
			'name' => 'Some name',
			'short_name' => 'SN',
			'vat' => 23,
		]);
	}

	public function testValidWithParent(): void {
		$this->giveModel([
			'name' => 'Some name',
			'short_name' => 'SN',
			'vat' => 23,
			'parent_id' => 1,
		]);
		$this->thenSuccessSave();
		$this->tester->seeRecord(IssueType::class, [
			'name' => 'Some name',
			'short_name' => 'SN',
			'vat' => 23,
			'parent_id' => 1,

		]);
	}

	public function getModel(): IssueTypeForm {
		return $this->model;
	}
}
