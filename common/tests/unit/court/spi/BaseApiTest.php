<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\entity\AppealInterface;
use common\tests\unit\Unit;

abstract class BaseApiTest extends Unit {

	const TEST_APPEAL = AppealInterface::APPEAL_WROCLAW;

	protected SPIApi $api;

	public function _before(): void {
		$this->api = SPIApi::testApi();
	}

}
