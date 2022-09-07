<?php

namespace backend\tests\unit\hint;

use backend\modules\hint\models\HintDistrictForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\HintFixtureHelper;
use common\fixtures\helpers\TerytFixtureHelper;
use common\models\hint\HintCity;
use common\tests\_support\UnitModelTrait;
use yii\base\Model;

class HintDistrictFormTest extends Unit {

	use UnitModelTrait;

	private HintDistrictForm $model;

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
		$this->thenSeeError('Region cannot be blank.', 'region_id');
		$this->thenSeeError('District cannot be blank.', 'district_id');
		$this->thenSeeError('User cannot be blank.', 'user_id');
		$this->thenSeeError('Type cannot be blank.', 'type');
	}

	public function testNotExistedRegion(): void {
		$this->giveModel([
			'region_id' => -1,
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Region is invalid.', 'region_id');
	}

	public function testNotExistedDistrictInRegion(): void {
		$this->giveModel([
			'region_id' => 22,
			'district_id' => 6,
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('District is invalid.', 'district_id');
	}

	public function testDistictWithNotIndependentCity(): void {
		$this->giveModel([
			'region_id' => 22,
			'district_id' => 8,
			'user_id' => 1,
			'type' => HintCity::TYPE_CARE_BENEFITS,
		]);
		$save = $this->model->save();
		$this->assertSame(4, $save);
		$this->tester->seeRecord(HintCity::class, [
			'user_id' => 1,
			'type' => HintCity::TYPE_CARE_BENEFITS,
			'city_id' => TerytFixtureHelper::SIMC_ID_CEWICE,
		]);
		$this->tester->dontSeeRecord(HintCity::class, [
			'user_id' => 1,
			'type' => HintCity::TYPE_CARE_BENEFITS,
			'city_id' => TerytFixtureHelper::SIMC_ID_CIEMIENIEC,
		]);
	}

	public function getModel(): Model {
		return $this->model;
	}

	private function giveModel(array $config) {
		$this->model = new HintDistrictForm($config);
	}
}
