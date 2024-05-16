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
	public const SIMC_ID_LEBORK = 977373;
	public const SIMC_ID_CEWICE = 741363;
	public const SIMC_ID_BUKOWINA = 741340;
	public const SIMC_ID_WEJHEROWO = 934984;
	public const SIMC_ID_BIELSKO_BIALA = 923584;
	public const SIMC_ID_CIEMIENIEC = 741601;
	public const SIMC_ID_PIESKI = 741587;

	public const SIMC_ID_BIALYSTOK = 922410;

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
