<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\models\AppealInterface;

class CourtTest extends BaseApiTest {

	public function testGetCourtsForGdanskAppeal() {
		//@todo in this moment Appal URLs not working in Test API
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

	/**
	 * @dataProvider appealsCourtsDataProvider
	 * @param string $appeal
	 * @param int $totalCount
	 * @return void
	 */
	public function testGetCourtsForAllAppeals(string $appeal, int $totalCount) {
		$api = $this->api;
		$api->setAppeal($appeal);
		$dataProvider = $this->api->getCourts();
		$this->tester->assertSame($totalCount, $dataProvider->getTotalCount());
	}

	public function testGetAllCourtDepartments() {
		$api = $this->api;
		$dataProvider = $api->getAllCourtDepartments();
		$this->tester->assertSame(204, $dataProvider->getTotalCount());
	}

	public function testGetCourtDepartmentsForSelectedCourt() {
		$api = $this->api;
		$dataProvider = $api->getCourtDepartments(1);
		$this->tester->assertSame(3, $dataProvider->getTotalCount());

		$dataProvider = $api->getCourtDepartments(2);
		$this->tester->assertSame(12, $dataProvider->getTotalCount());
	}

	public function testGetDepartmentRepertory(): void {
		$api = $this->api;
		$dataProvider = $api->getDepartmentRepertories(1);
		$this->tester->assertSame(10, $dataProvider->getTotalCount());
	}

	public function appealsCourtsDataProvider(): array {
		//@todo in this moment Appal URLs not working in Test API, only Wroclaw Appeal
		return [
			AppealInterface::APPEAL_BIALYSTOK => [AppealInterface::APPEAL_BIALYSTOK, 32],
			AppealInterface::APPEAL_GDANSK => [AppealInterface::APPEAL_GDANSK, 49],
			AppealInterface::APPEAL_KATOWICE => [AppealInterface::APPEAL_KATOWICE, 37],
			AppealInterface::APPEAL_KRAKOW => [AppealInterface::APPEAL_KRAKOW, 38],
			AppealInterface::APPEAL_LUBLIN => [AppealInterface::APPEAL_LUBLIN, 35],
			AppealInterface::APPEAL_LODZ => [AppealInterface::APPEAL_LODZ, 41],
			AppealInterface::APPEAL_POZNAN => [AppealInterface::APPEAL_POZNAN, 36],
			AppealInterface::APPEAL_RZESZOW => [AppealInterface::APPEAL_RZESZOW, 25],
			AppealInterface::APPEAL_SZCZECIN => [AppealInterface::APPEAL_SZCZECIN, 27],
			AppealInterface::APPEAL_WARSZAWA => [AppealInterface::APPEAL_WARSZAWA, 17],
			AppealInterface::APPEAL_WROCLAW => [AppealInterface::APPEAL_WROCLAW, 42],
		];
	}

}
