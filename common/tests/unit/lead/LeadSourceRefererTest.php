<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\LeadSource;
use common\tests\unit\Unit;

class LeadSourceRefererTest extends Unit {

	public function _fixtures(): array {
		return LeadFixtureHelper::source();
	}

	public function testRefererWithWWW(): void {
		$source = LeadSource::findByReferer('https://www.test.com');
		$this->tester->assertNotEmpty($source);
		$this->tester->assertSame('https://www.test.com', $source->url);
	}

	public function testRefererWithoutWWW(): void {
		$source = LeadSource::findByReferer('https://test.com');
		$this->tester->assertNotEmpty($source);
		$this->tester->assertSame('https://www.test.com', $source->url);
	}
}
