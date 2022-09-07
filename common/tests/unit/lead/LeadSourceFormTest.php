<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadSourceForm;
use common\modules\lead\models\LeadSource;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;

class LeadSourceFormTest extends Unit {

	use UnitModelTrait;

	private LeadSourceForm $model;

	public function _fixtures(): array {
		return LeadFixtureHelper::source();
	}

	public function getModel(): Model {
		return $this->model;
	}

	public function testEmpty(): void {
		$this->giveModel([]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Name cannot be blank.', 'name');
		$this->thenSeeError('Type cannot be blank.', 'type_id');
	}

	public function testInvalidType(): void {
		$this->giveModel([
			'name' => 'New source',
			'type_id' => 12121212,
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Type is invalid.', 'type_id');
	}

	public function testNotValidUrl(): void {
		$this->giveModel([
			'name' => 'Not valid url',
			'type_id' => 1,
			'url' => 'notvalidurl',
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Url is not a valid URL.', 'url');
	}

	public function testWithoutURL(): void {
		$this->giveModel([
			'name' => 'New source',
			'type_id' => 1,
		]);

		$this->thenSuccessValidate();
		$this->thenSuccessSave();
		$this->thenSeeSource([
			'name' => 'New source',
			'type_id' => 1,
		]);
	}

	public function testWithURL(): void {
		$this->giveModel([
			'name' => 'New source',
			'type_id' => 1,
			'url' => 'http://google.com',
		]);

		$this->thenSuccessValidate();
		$this->thenSuccessSave();
		$this->thenSeeSource([
			'name' => 'New source',
			'type_id' => 1,
			'url' => 'http://google.com',
		]);
	}

	public function testNotUniqueName(): void {
		$this->giveModel([
			'name' => 'Test source',
			'type_id' => 1,
		]);
		$this->thenSuccessSave();
		$this->giveModel([
			'name' => 'Test source',
			'type_id' => 2,
		]);
		$this->thenUnsuccessValidate();
	}

	public function testWithOwner(): void {
		$owner_id = array_key_first(LeadSourceForm::getUsersNames());
		$this->giveModel([
			'name' => 'Test source',
			'type_id' => 1,
			'owner_id' => $owner_id,
		]);
		$this->thenSuccessSave();
		$this->thenSeeSource([
			'name' => 'Test source',
			'type_id' => 1,
			'owner_id' => $owner_id,
		]);
	}

	public function testWithPhone(): void {
		$this->giveModel([
			'name' => 'Test source',
			'type_id' => 1,
			'phone' => '123-456-789',
		]);
		$this->thenSuccessSave();
		$this->thenSeeSource([
			'name' => 'Test source',
			'type_id' => 1,
			'phone' => '+48 12 345 67 89',
		]);
	}

	public function thenSeeSource(array $attributes): void {
		$this->tester->seeRecord(LeadSource::class, $attributes);
	}

	private function giveModel(array $config): void {
		$this->model = new LeadSourceForm($config);
	}
}
