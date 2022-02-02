<?php

namespace common\fixtures\helpers;

interface FixtureTester {

	public function haveFixtures($fixtures);

	public function grabFixture($name, $index = null);

	public function seeRecord(string $class, array $array);

	/**
	 * @param string $className
	 * @param array $attributes
	 * @return mixed
	 */
	public function haveRecord(string $className, array $attributes);

}
