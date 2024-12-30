<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\components\exceptions\UnauthorizedSPIApiException;
use common\modules\court\modules\spi\models\AppealInterface;

class SPIApiTest extends BaseApiTest {

	public function testAuthenticateWithTestUsernameAndPassword(): void {
		$api = $this->api;
		$this->tester->assertTrue($api->authenticate());
	}

	public function testAuthWithoutValidData() {
		$this->tester->expectThrowable(UnauthorizedSPIApiException::class, function () {
			$this->api->username = 'not-existed-user-name';
			$this->api->password = 'test-password';
			$this->api->authenticate();
		});
	}

	public function testAuthWithoutValidDataWithoutThrown() {
		$this->api->username = 'not-existed-user-name';
		$this->api->password = 'test-password';
		$this->api->authenticate(false);
		$this->tester->assertFalse($this->api->authenticate(false));
	}

	public function testAuthOnOtherAppeal(): void {
		//@todo test api has not other appeal urls.
		$this->api->baseUrl = $this->api->getAppealUrl(AppealInterface::APPEAL_GDANSK);
		$this->tester->expectThrowable(UnauthorizedSPIApiException::class, function () {
			$this->api->authenticate();
		});
	}

}
