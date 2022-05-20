<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\LeadSource;
use common\tests\unit\Unit;

class LeadSourceTest extends Unit {

	public function _fixtures(): array {
		return LeadFixtureHelper::source();
	}

	public function testURLWithWWW(): void {
		$source = LeadSource::findByURL('https://www.test.com');
		$this->tester->assertNotEmpty($source);
		$this->tester->assertSame('https://www.test.com', $source->url);
	}

	public function testURLWithoutWWW(): void {
		$source = LeadSource::findByURL('https://test.com');
		$this->tester->assertNotEmpty($source);
		$this->tester->assertSame('https://www.test.com', $source->url);
	}
}
