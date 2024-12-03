<?php

namespace common\fixtures\helpers;

use common\fixtures\court\CourtFixture;
use common\fixtures\court\LawsuitFixture;
use Yii;

class CourtFixtureHelper extends BaseFixtureHelper {

	public const COURT = 'court.court';
	public const LAWSUIT = 'court.lawsuit';

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/court/');
	}

	public static function court(): array {
		return [
			static::COURT => [
				'class' => CourtFixture::class,
				'dataFile' => static::dataDir() . 'court.php',
			],
		];
	}

	public static function lawsuit(): array {
		return [
			static::LAWSUIT => [
				'class' => LawsuitFixture::class,
				'dataFile' => static::dataDir() . 'lawsuit.php',
			],
		];
	}

}
