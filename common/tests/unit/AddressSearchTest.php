<?php

namespace common\tests\unit;

use common\fixtures\AddressFixture;
use common\fixtures\helpers\TerytFixtureHelper;
use common\models\AddressSearch;
use common\tests\_support\UnitSearchModelTrait;

class AddressSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before() {
		parent::_before();
		$this->model = $this->createModel();
		$this->tester->haveFixtures(
			array_merge(
				TerytFixtureHelper::fixtures(),
				[
					'address' => [
						'class' => AddressFixture::class,
						'dataFile' => codecept_data_dir() . 'address.php',
					],
				]
			)
		);
	}

	public function testEmpty(): void {
		$this->assertTotalCount(5);
	}

	public function testPostalCode(): void {
		$this->assertTotalCount(3, ['postal_code' => '84-']);
		$this->assertTotalCount(2, ['postal_code' => '84-3']);
		$this->assertTotalCount(0, ['postal_code' => '12-3']);
	}

	public function testRegion(): void {
		$this->assertTotalCount(3, ['region_id' => 22]);
		$this->assertTotalCount(2, ['region_id' => 24]);
		$this->assertTotalCount(0, ['region_id' => 1515]);
	}

	public function testCityName(): void {
		$this->assertTotalCount(2, ['city_name' => 'Lę']);
		$this->assertTotalCount(0, ['city_name' => 'bork']);
		$this->assertTotalCount(1, ['city_name' => 'Wej']);
		$this->assertTotalCount(2, ['city_name' => 'Biel']);
	}

	protected function createModel(): AddressSearch {
		return new AddressSearch();
	}
}
