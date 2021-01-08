<?php

namespace common\fixtures\helpers;

use common\fixtures\user\AgentFixture;
use common\fixtures\user\CustomerFixture;
use common\fixtures\user\LawyerFixture;
use common\fixtures\user\TelemarketerFixture;
use common\fixtures\UserProfileFixture;
use common\fixtures\UserTraitFixture;
use Yii;

class UserFixtureHelper {

	public const CUSTOMER_JOHN_WAYNE_ID = 100;
	public const CUSTOMER_ERIKA_LARSON_ID = 101;
	public const CUSTOMER_TOMMY_JOHNS = 102;

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/user/');
	}

	public static function agent(): array {
		return [
			'class' => AgentFixture::class,
			'dataFile' => static::dataDir() . 'agent.php',
		];
	}

	public static function telemarketer(): array {
		return [
			'class' => TelemarketerFixture::class,
			'dataFile' => static::dataDir() . 'telemarketer.php',
		];
	}

	public static function customer(): array {
		return [
			'class' => CustomerFixture::class,
			'dataFile' => static::dataDir() . 'customer.php',
		];
	}

	public static function customerProfile(): array {
		return [
			'class' => UserProfileFixture::class,
			'dataFile' => static::dataDir() . 'customer_profile.php',
		];
	}

	public static function customerTraits(): array {
		return [
			'class' => UserTraitFixture::class,
			'dataFile' => static::dataDir() . 'customer_trait.php',
		];
	}

	public static function lawyer(): array {
		return [
			'class' => LawyerFixture::class,
			'dataFile' => static::dataDir() . 'lawyer.php',
		];
	}

	public static function addPermission(array &$fixtureConfig, string $permissionName): void {
		if (!isset($fixtureConfig['permissions'])) {
			$fixtureConfig['permissions'] = [];
		}
		if (!in_array($permissionName, $fixtureConfig['permissions'], true)) {
			$fixtureConfig['permissions'][] = $permissionName;
		}
	}

}
