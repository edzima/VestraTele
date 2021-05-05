<?php

namespace common\fixtures\helpers;

use common\fixtures\hint\HintCityFixture;
use common\fixtures\hint\HintSourceFixture;
use common\fixtures\hint\HintUserFixture;
use Yii;

class HintFixtureHelper {

	public const SOURCE = 'hint-source';
	public const USER = 'hint-user';
	public const CITY = 'hint-city';

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/hint/');
	}

	public static function source(): array {
		return [
			static::SOURCE => [
				'class' => HintSourceFixture::class,
				'dataFile' => static::dataDir() . 'source.php',
			],
		];
	}

	public static function user(): array {
		return [
			static::USER => [
				'class' => HintUserFixture::class,
				'dataFile' => static::dataDir() . 'user.php',
			],
		];
	}

	public static function city(): array {
		return [
			static::CITY => [
				'class' => HintCityFixture::class,
				'dataFile' => static::dataDir() . 'city.php',
			],
		];
	}

}
