<?php

namespace common\fixtures\helpers;

use common\fixtures\provision\ProvisionFixture;
use common\fixtures\provision\ProvisionTypeFixture;
use Yii;

class ProvisionFixtureHelper {

	public const PROVISION = 'provision';
	public const TYPE = 'provision-type';

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/provision/');
	}

	public static function provision(): array {
		return [
			static::PROVISION => [
				'class' => ProvisionFixture::class,
				'dataFile' => static::dataDir() . 'provision.php',
			],
			static::TYPE => [
				'class' => ProvisionTypeFixture::class,
				'dataFile' => static::dataDir() . 'type.php',
			],
		];
	}
}
