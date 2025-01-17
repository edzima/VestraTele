<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\components\SPIApi;
use common\tests\unit\Unit;

abstract class BaseApiTest extends Unit {

	protected SPIApi $api;

	public function _before(): void {
		$this->api = SPIApi::testApi();
	}

}
