<?php

namespace common\fixtures\helpers;

use common\fixtures\user\AgentFixture;
use common\fixtures\user\CustomerFixture;
use common\fixtures\user\LawyerFixture;
use common\fixtures\user\TelemarketerFixture;
use common\fixtures\UserFixture;
use common\fixtures\UserProfileFixture;
use common\fixtures\UserTraitFixture;
use Yii;

class UserFixtureHelper {

	public const CUSTOMER_JOHN_WAYNE_ID = 100;
	public const CUSTOMER_ERIKA_LARSON_ID = 101;
	public const CUSTOMER_TOMMY_JOHNS = 102;
	public const CUSTOMER_JOHN_LENNON = 103;

	public const AGENT_PETER_NOWAK = 300;
	public const AGENT_AGNES_MILLER = 301;
	public const AGENT_TOMMY_SET = 302;
	public const AGENT_EMILY_PAT = 303;

	public const WORKER_AGENT = 'agent';
	public const WORKER_LAWYER = 'lawyer';
	public const WORKER_TELEMARKETER = 'telemarketer';

	public const CUSTOMER = 'customer';

	public const MANAGER_JOHN = 500;
	public const MANAGER_NICOLE = 501;

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/user/');
	}

	public static function workers(): array {
		return [
			static::WORKER_AGENT => static::agent(),
			static::WORKER_LAWYER => static::lawyer(),
			static::WORKER_TELEMARKETER => static::telemarketer(),
		];
	}

	public static function agent(): array {
		return [
			'class' => AgentFixture::class,
			'dataFile' => static::dataDir() . 'agent.php',
		];
	}

	public static function profile(string $type): array {
		return [
			'class' => UserProfileFixture::class,
			'dataFile' => static::dataDir() . $type . '_profile.php',
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

	/**
	 * @return string[]
	 * @todo maybe user RbacUserFixture with Role User::ROLE_MANAGER
	 */
	public static function manager(): array {
		return [
			'class' => UserFixture::class,
			'dataFile' => static::dataDir() . 'manager.php',
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
