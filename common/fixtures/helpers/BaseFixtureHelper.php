<?php

namespace common\fixtures\helpers;

use Yii;
use yii\base\BaseObject;

class BaseFixtureHelper extends BaseObject {

	protected FixtureTester $tester;

	public function __construct(FixtureTester $tester, $config = []) {
		$this->tester = $tester;
		parent::__construct($config);
	}

	public function have(array $fixtures) {
		return $this->tester->haveFixtures($fixtures);
	}

	public static function getDataDirPath(string $path = null): string {
		if ($path) {
			return $path;
		}
		return static::getDefaultDataDirPath();
	}

	protected static function getDefaultDataDirPath(): string {
		return Yii::getAlias('@common/tests/_data/');
	}

}
