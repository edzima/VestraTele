<?php

namespace common\tests\unit;

use Codeception\Module\Yii2;
use Codeception\Test\Unit as BaseUnit;
use common\tests\UnitTester;

abstract class Unit extends BaseUnit {

	/* @var UnitTester */
	protected $tester;

	/**
	 * @return array
	 * @see Yii2::loadFixtures()
	 */
	public function _fixtures(): array {
		return [];
	}

}
