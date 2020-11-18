<?php

namespace common\fixtures\helpers;

use common\fixtures\issue\EntityResponsibleFixture;
use common\fixtures\issue\IssueFixture;
use common\fixtures\issue\IssueUserFixture;
use common\fixtures\issue\StageFixture;
use common\fixtures\issue\StageTypesFixtures;
use common\fixtures\issue\TypeFixture;
use common\fixtures\user\AgentFixture;
use common\fixtures\user\CustomerFixture;
use common\fixtures\user\LawyerFixture;
use common\fixtures\user\TelemarketerFixture;
use common\fixtures\UserProfileFixture;
use common\models\user\User;
use Yii;

class IssueFixtureHelper {

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/');
	}

	public static function fixtures(): array {
		return [
			'issue' => [
				'class' => IssueFixture::class,
				'dataFile' => static::dataDir() . 'issue/issue.php',
			],
			'user' => [
				'class' => IssueUserFixture::class,
				'dataFile' => static::dataDir() . 'issue/users.php',

			],
			'customer' => [
				'class' => CustomerFixture::class,
				'dataFile' => static::dataDir() . 'user/customer.php',
			],
			'customer-profile' => [
				'class' => UserProfileFixture::class,
				'dataFile' => static::dataDir() . 'user/customer_profile.php',
			],
			'agent' => [
				'class' => AgentFixture::class,
				'dataFile' => static::dataDir() . 'user/agent.php',
				'permissions' => [User::PERMISSION_ISSUE],
			],
			'lawyer' => [
				'class' => LawyerFixture::class,
				'dataFile' => static::dataDir() . 'user/lawyer.php',
				'permissions' => [User::PERMISSION_ISSUE],
			],
			'telemarketer' => [
				'class' => TelemarketerFixture::class,
				'dataFile' => static::dataDir() . 'user/telemarketer.php',
				'permissions' => [User::PERMISSION_ISSUE],
			],
			'stage' => [
				'class' => StageFixture::class,
				'dataFile' => static::dataDir() . 'issue/stage.php',
			],
			'type' => [
				'class' => TypeFixture::class,
				'dataFile' => static::dataDir() . 'issue/type.php',
			],
			'stage-types' => [
				'class' => StageTypesFixtures::class,
				'dataFile' => static::dataDir() . 'issue/stage_types.php',
			],
			'entity' => [
				'class' => EntityResponsibleFixture::class,
				'dataFile' => static::dataDir() . 'issue/entity_responsible.php',
			],
		];
	}

	public static function stageAndTypesFixtures(): array {
		return [
			'stage' => [
				'class' => StageFixture::class,
				'dataFile' => static::dataDir() . 'issue/stage.php',
			],
			'type' => [
				'class' => TypeFixture::class,
				'dataFile' => static::dataDir() . 'issue/type.php',
			],
			'stage-types' => [
				'class' => StageTypesFixtures::class,
				'dataFile' => static::dataDir() . 'issue/stage_types.php',
			],
		];
	}
}
