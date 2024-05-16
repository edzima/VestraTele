<?php

namespace common\tests\unit\court;

use common\modules\court\components\CourtApi;
use common\tests\unit\Unit;

class CourtApiTest extends Unit {

	private CourtApi $importer;

	public function _before() {
		parent::_before();
		$this->importer = new CourtApi();
	}

	public function testGetLastUpdateDateFromApi() {
		$date = $this->importer->getLastUpdateDate();
		$this->tester->assertNotFalse(strtotime($date));
	}

	public function testGetFileUrlFromApi() {
		$url = $this->importer->getFileUrl();
		$this->tester->assertNotEmpty($url);
	}

	public function testApiData() {
		$data = $this->importer->getApiData();
		$this->tester->assertIsArray($data);
		$this->tester->assertArrayHasKey('jsonapi', $data);
		$this->tester->assertArrayHasKey('links', $data);
		$this->tester->assertArrayHasKey('meta', $data);
		$this->tester->assertArrayHasKey('data', $data);
	}
}
