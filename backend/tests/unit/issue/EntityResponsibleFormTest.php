<?php

namespace backend\tests\unit\issue;

use backend\modules\entityResponsible\models\EntityResponsibleForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\Address;
use common\models\entityResponsible\EntityResponsible;
use common\tests\_support\UnitModelTrait;

class EntityResponsibleFormTest extends Unit {

	use UnitModelTrait;

	private EntityResponsibleForm $model;

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::entityResponsible(),
		);
	}

	public function testEmpty(): void {
		$this->giveModel([]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Name cannot be blank.', 'name');
	}

	public function testOnlyName(): void {
		$this->giveModel([
			'name' => 'Test name',
		]);
		$this->thenSuccessSave();
		$this->tester->seeRecord(EntityResponsible::class, [
			'name' => 'Test name',
		]);
	}

	public function testNameWithDetails(): void {
		$this->giveModel([
			'name' => 'Test name',
			'details' => 'Some det',

		]);
		$this->thenSuccessSave();
		$this->tester->seeRecord(EntityResponsible::class, [
			'name' => 'Test name',
			'details' => 'Some det',
		]);
	}

	public function testWithoutAddress(): void {
		$this->giveModel([
			'name' => 'Test name',
		]);
		$this->thenSuccessSave();

		$model = $this->tester->grabRecord(EntityResponsible::class, [
			'name' => 'Test name',
		]);

		$this->tester->assertNull($model->address);
	}

	public function testWithAddress(): void {
		$this->giveModel([
			'name' => 'Test name',
		]);
		$this->model->getAddress()->postal_code = '11-111';
		$this->model->getAddress()->info = 'entity address info';
		$this->thenSuccessSave();

		$this->tester->seeRecord(Address::class, [
			'postal_code' => '11-111',
			'info' => 'entity address info',
		]);

		$model = $this->tester->grabRecord(EntityResponsible::class, [
			'name' => 'Test name',
		]);

		$address = $model->address;

		$this->tester->assertInstanceOf(Address::class, $address);
		$this->tester->assertSame($address->id, $model->address_id);
		$this->tester->assertSame('11-111', $address->postal_code);
		$this->tester->assertSame('entity address info', $address->info);
	}

	public function getModel(): EntityResponsibleForm {
		return $this->model;
	}

	private function giveModel(array $config = []): void {
		$this->model = new EntityResponsibleForm($config);
	}
}
