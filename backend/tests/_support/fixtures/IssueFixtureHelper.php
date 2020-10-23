<?php

namespace backend\tests\fixtures;

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

class IssueFixtureHelper {

	public static function fixtures(): array {
		return [
			'issue' => [
				'class' => IssueFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/issue.php',
			],
			'user' => [
				'class' => IssueUserFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/users.php',

			],
			'customer' => [
				'class' => CustomerFixture::class,
				'dataFile' => codecept_data_dir() . 'customer.php',
			],
			'customer-profile' => [
				'class' => UserProfileFixture::class,
				'dataFile' => codecept_data_dir() . 'customer_profile.php',
			],
			'agent' => [
				'class' => AgentFixture::class,
				'dataFile' => codecept_data_dir() . 'agent.php',
				'permissions' => [User::PERMISSION_ISSUE],
			],
			'lawyer' => [
				'class' => LawyerFixture::class,
				'dataFile' => codecept_data_dir() . 'lawyer.php',
				'permissions' => [User::PERMISSION_ISSUE],
			],
			'telemarketer' => [
				'class' => TelemarketerFixture::class,
				'dataFile' => codecept_data_dir() . 'telemarketer.php',
				'permissions' => [User::PERMISSION_ISSUE],
			],
			'stage' => [
				'class' => StageFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/stage.php',
			],
			'type' => [
				'class' => TypeFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/type.php',
			],
			'stage-types' => [
				'class' => StageTypesFixtures::class,
				'dataFile' => codecept_data_dir() . 'issue/stage_types.php',
			],
			'entity' => [
				'class' => EntityResponsibleFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/entity_responsible.php',
			],
		];
	}
}
