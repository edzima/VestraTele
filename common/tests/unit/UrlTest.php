<?php

namespace common\tests\unit;

use frontend\helpers\Url as FrontendUrl;
use backend\helpers\Url as BackendUrl;

class UrlTest extends Unit {

	public function testIssue(): void {
		$this->tester->assertSame('/issue/view?id=1', FrontendUrl::issueView(1));
		$this->tester->assertSame('/issue/issue/view?id=1', BackendUrl::issueView(1));
		$this->tester->assertSame('http://frontend.dev/issue/view?id=1', FrontendUrl::issueView(1, true));
		$this->tester->assertSame('http://backend.dev/issue/issue/view?id=1', BackendUrl::issueView(1, true));
	}
}
