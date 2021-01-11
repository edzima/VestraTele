<?php

namespace common\fixtures\helpers;

use common\fixtures\teryt\SimcFixture;
use common\fixtures\teryt\TercFixture;
use Yii;

/**
 * Class TerytFixtureHelper
 *
 * @todo move to edzima\teryt
 */
class TerytFixtureHelper {

	public const SIMC_ID_DUCHOWO = 877660;
	public const SIMC_ID_POSTOLIN = 878116;

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/teryt/');
	}

	public static function fixtures(): array {
		return [
			'terc' => [
				'class' => TercFixture::class,
				'dataFile' => static::dataDir() . 'terc.php',
			],
			'simc' => [
				'class' => SimcFixture::class,
				'dataFile' => static::dataDir() . 'simc.php',
			],
		];
	}

}
