<?php

namespace common\fixtures\helpers;

interface FixtureTester {

	public function haveFixtures($fixtures);

	public function grabFixture($name, $index);

	public function seeRecord(string $class, array $array);

}
