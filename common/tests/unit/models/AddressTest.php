<?php

namespace common\tests\unit\models;

use common\models\Address;
use common\tests\unit\Unit;

/**
 * Address model test
 */
class AddressTest extends Unit {

	public function testEmpty(): void {
		$model = new Address();
		$this->tester->assertFalse($model->validate());
	}

}
