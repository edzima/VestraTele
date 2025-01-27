<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\entity\AppealInterface;

class AppealTest extends BaseApiTest {

	public function testGetAppealUrl() {
		$api = $this->api;
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/bialystok/api',
			$api->getAppealUrl(AppealInterface::APPEAL_BIALYSTOK)
		);
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/gdansk/api',
			$api->getAppealUrl(AppealInterface::APPEAL_GDANSK)
		);
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/katowice/api',
			$api->getAppealUrl(AppealInterface::APPEAL_KATOWICE)
		);
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/krakow/api',
			$api->getAppealUrl(AppealInterface::APPEAL_KRAKOW)
		);
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/lublin/api',
			$api->getAppealUrl(AppealInterface::APPEAL_LUBLIN)
		);
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/lodz/api',
			$api->getAppealUrl(AppealInterface::APPEAL_LODZ)
		);
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/poznan/api',
			$api->getAppealUrl(AppealInterface::APPEAL_POZNAN)
		);
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/rzeszow/api',
			$api->getAppealUrl(AppealInterface::APPEAL_RZESZOW)
		);
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/szczecin/api',
			$api->getAppealUrl(AppealInterface::APPEAL_SZCZECIN)
		);
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/warszawa/api',
			$api->getAppealUrl(AppealInterface::APPEAL_WARSZAWA)
		);
		$this->tester->assertSame(
			'https://portal.wroclaw.sa.gov.pl/wroclaw/api',
			$api->getAppealUrl(AppealInterface::APPEAL_WROCLAW)
		);
	}
}
