<?php

namespace common\fixtures\helpers;

use common\fixtures\issue\EntityResponsibleFixture;
use common\fixtures\issue\IssueFixture;
use common\fixtures\issue\IssueUserFixture;
use common\fixtures\issue\StageFixture;
use common\fixtures\issue\StageTypesFixtures;
use common\fixtures\issue\TypeFixture;
use common\fixtures\settlement\CalculationFixture;
use common\fixtures\settlement\PayFixture;
use common\fixtures\user\AgentFixture;
use common\fixtures\user\CustomerFixture;
use common\fixtures\user\LawyerFixture;
use common\fixtures\user\TelemarketerFixture;
use common\fixtures\UserProfileFixture;
use common\models\user\User;
use Yii;

class IssueFixtureHelper {

	public const ISSUE_COUNT = 6;
	public const ARCHIVED_ISSUE_COUNT = 1;

	public const AGENT = 'agent';
	public const CALCULATION = 'calculation';
	public const PAY = 'pay';
	public const ISSUE = 'issue';

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/');
	}

	public static function fixtures(): array {
		return array_merge([
			static::ISSUE => [
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
			static::AGENT => [
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
			'entity' => [
				'class' => EntityResponsibleFixture::class,
				'dataFile' => static::dataDir() . 'issue/entity_responsible.php',
			],
		], static::stageAndTypesFixtures());
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

	public static function settlements(): array {
		return [
			static::CALCULATION => [
				'class' => CalculationFixture::class,
				'dataFile' => static::dataDir() . 'settlement/calculation.php',
			],
			static::PAY => [
				'class' => PayFixture::class,
				'dataFile' => static::dataDir() . 'settlement/pay.php',
			],
		];
	}

}
