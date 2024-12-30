<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\models\AppealInterface;

class CourtTest extends BaseApiTest {

	public function testGetCourtsFirGdanskAppeal() {
		$this->api->setAppeal(AppealInterface::APPEAL_GDANSK);
		$dataProvider = $this->api->getCourts();
		$first = $dataProvider->getModels()[0];
		$this->tester->assertSame('Sąd Apelacyjny w Gdańsku', $first['name']);
		$this->tester->assertSame(49, $dataProvider->getTotalCount());
	}

	public function testGetCourtsForWroclawAppeal(): void {
		$this->api->setAppeal(AppealInterface::APPEAL_WROCLAW);
		$dataProvider = $this->api->getCourts();
		$this->tester->assertSame(42, $dataProvider->getTotalCount());
		$this->tester->assertSame(20, count($dataProvider->getModels()));
	}

	public function testGetCourtsWithNameParams() {
		$dataProvider = $this->api->getCourts([
			'name.contains' => 'Sąd Apelacyjny',
		]);
		$models = $dataProvider->getModels();
		$this->tester->assertSame(1, count($models));
	}

	public function testGetCourtDepartments() {
		$api = $this->api;
		$dataProvider = $api->getCourtDepartments();
	}

}
