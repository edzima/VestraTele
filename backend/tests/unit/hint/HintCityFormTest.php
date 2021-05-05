<?php

namespace backend\tests\unit\hint;

use backend\modules\hint\models\HintCityForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\HintFixtureHelper;
use common\fixtures\helpers\TerytFixtureHelper;
use common\models\hint\HintCity;
use common\tests\_support\UnitModelTrait;
use yii\base\Model;

class HintCityFormTest extends Unit {

	use UnitModelTrait;

	private HintCityForm $model;

	public function _fixtures(): array {
		return array_merge(
			TerytFixtureHelper::fixtures(),
			HintFixtureHelper::city(),
			HintFixtureHelper::user()
		);
	}

	public function testEmpty(): void {
		$this->giveModel([]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('City cannot be blank.', 'city_id');
		$this->thenSeeError('User cannot be blank.', 'user_id');
	}

	public function testCityWithoutHint(): void {
		$this->giveModel([
			'city_id' => TerytFixtureHelper::SIMC_ID_LEBORK,
			'user_id' => 1,
			'details' => 'New lebork hint.',
		]);

		$this->thenSuccessSave();
		$this->thenSeeRecord([
			'city_id' => TerytFixtureHelper::SIMC_ID_BIELSKO_BIALA,
			'user_id' => 1,
			'status' => HintCity::STATUS_NEW,
			'type' => HintCity::TYPE_CARE_BENEFITS,
		]);
	}

	public function testAbandonedWithoutDetails(): void {
		$this->giveModel([
			'city_id' => TerytFixtureHelper::SIMC_ID_LEBORK,
			'user_id' => 1,
			'status' => HintCity::STATUS_ABANDONED,
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Details cannot be blank when status is abandoned.', 'details');
	}

	public function testAbandonedWithDetails(): void {
		$this->giveModel([
			'city_id' => TerytFixtureHelper::SIMC_ID_LEBORK,
			'user_id' => 1,
			'status' => HintCity::STATUS_ABANDONED,
			'details' => 'Not found active targets.',
		]);

		$this->thenSuccessSave();

		$this->thenSeeRecord([
			'city_id' => TerytFixtureHelper::SIMC_ID_LEBORK,
			'user_id' => 1,
			'status' => HintCity::STATUS_ABANDONED,
			'details' => 'Not found active targets.',
		]);
	}

	public function testSameTypeUserAndCity(): void {
		$this->giveModel([
			'city_id' => TerytFixtureHelper::SIMC_ID_LEBORK,
			'user_id' => 1,
			'type' => HintCity::TYPE_CARE_BENEFITS,
		]);
		$this->thenSuccessSave();
		$this->giveModel([
			'city_id' => TerytFixtureHelper::SIMC_ID_LEBORK,
			'user_id' => 1,
			'type' => HintCity::TYPE_CARE_BENEFITS,
		]);
		$this->thenUnsuccessValidate();

		$this->tester->assertTrue($this->getModel()->hasErrors('user_id'));
		$this->tester->assertTrue($this->getModel()->hasErrors('type'));
		$this->tester->assertTrue($this->getModel()->hasErrors('city_id'));
	}

	public function testSameTypeUserAndOtherCity(): void {
		$this->giveModel([
			'city_id' => TerytFixtureHelper::SIMC_ID_LEBORK,
			'user_id' => 1,
			'type' => HintCity::TYPE_CARE_BENEFITS,
		]);
		$this->thenSuccessSave();
		$this->giveModel([
			'city_id' => TerytFixtureHelper::SIMC_ID_CEWICE,
			'user_id' => 1,
			'type' => HintCity::TYPE_CARE_BENEFITS,
		]);
		$this->thenSuccessSave();
	}

	public function testSameUserAndCityWithOtherType(): void {
		$this->giveModel([
			'city_id' => TerytFixtureHelper::SIMC_ID_LEBORK,
			'user_id' => 1,
			'type' => HintCity::TYPE_CARE_BENEFITS,
		]);
		$this->thenSuccessSave();
		$this->giveModel([
			'city_id' => TerytFixtureHelper::SIMC_ID_LEBORK,
			'user_id' => 1,
			'type' => HintCity::TYPE_COMMISSION_REFUNDS,
		]);
		$this->thenSuccessSave();
	}

	public function testEmptyType(): void {
		$this->giveModel(['type' => '']);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Type cannot be blank.', 'type');
	}

	public function testEmptyStatus(): void {
		$this->giveModel(['status' => '']);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Status cannot be blank.', 'status');
	}

	private function thenSeeRecord(array $attributes): void {
		$this->tester->seeRecord(HintCity::class, $attributes);
	}

	private function giveModel(array $config): void {
		$this->model = new HintCityForm($config);
	}

	public function getModel(): Model {
		return $this->model;
	}
}
