<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\components\SPIApi;
use common\tests\unit\Unit;

abstract class BaseApiTest extends Unit {

	protected SPIApi $api;

	protected const TEST_BASE_URL = 'https://testapi.wroclaw.sa.gov.pl/api/';
	protected const TEST_LOGIN = '83040707012';
	protected const TEST_PASSWORD = 'Wroclaw123';

	public function _before(): void {
		$this->api = new SPIApi();
		$this->api->baseUrl = static::TEST_BASE_URL;
		$this->api->username = static::TEST_LOGIN;
		$this->api->password = static::TEST_PASSWORD;
	}

}
