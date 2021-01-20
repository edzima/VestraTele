<?php

namespace common\fixtures\helpers;

use common\fixtures\issue\EntityResponsibleFixture;
use common\fixtures\issue\IssueFixture;
use common\fixtures\issue\IssueUserFixture;
use common\fixtures\issue\StageFixture;
use common\fixtures\issue\StageTypesFixtures;
use common\fixtures\issue\SummonFixture;
use common\fixtures\issue\TypeFixture;
use common\fixtures\settlement\CalculationCostFixture;
use common\fixtures\settlement\CalculationFixture;
use common\fixtures\settlement\CostFixture;
use common\fixtures\settlement\PayFixture;
use common\fixtures\settlement\PayReceivedFixture;
use common\models\user\User;
use Yii;

class IssueFixtureHelper {

	public const ISSUE_COUNT = 6;
	public const ARCHIVED_ISSUE_COUNT = 1;

	public const AGENT = 'agent';
	public const CUSTOMER = 'customer';
	public const LAWYER = 'lawyer';
	public const TELEMARKETER = 'telemarketer';

	public const CALCULATION = 'calculation';
	public const PAY = 'pay';
	public const ISSUE = 'issue';
	public const PAY_RECEIVED = 'pay-received';

	public const SUMMON = 'summon';

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/');
	}

	public static function fixtures(): array {
		return array_merge(
			[
				static::ISSUE => [
					'class' => IssueFixture::class,
					'dataFile' => static::dataDir() . 'issue/issue.php',
				],
			],
			static::entityResponsible(),
			static::stageAndTypesFixtures(),
			static::users(),
		);
	}

	public static function entityResponsible(): array {
		return [
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

	public static function users(): array {
		$users = [
			static::AGENT => UserFixtureHelper::agent(),
			static::CUSTOMER => UserFixtureHelper::customer(),
			static::LAWYER => UserFixtureHelper::lawyer(),
			static::TELEMARKETER => UserFixtureHelper::telemarketer(),
		];
		foreach ($users as &$user) {
			UserFixtureHelper::addPermission($user, User::PERMISSION_ISSUE);
		}
		$users['customer-profile'] = UserFixtureHelper::customerProfile();
		$users['users'] = [
			'class' => IssueUserFixture::class,
			'dataFile' => static::dataDir() . 'issue/users.php',
		];

		return $users;
	}

	public static function settlements(bool $withCost = false): array {
		$fixtures = [
			static::CALCULATION => [
				'class' => CalculationFixture::class,
				'dataFile' => static::dataDir() . 'settlement/calculation.php',
			],
			static::PAY => [
				'class' => PayFixture::class,
				'dataFile' => static::dataDir() . 'settlement/pay.php',
			],
		];
		if ($withCost) {
			$fixtures['cost'] = [
				'class' => CostFixture::class,
				'dataFile' => static::dataDir() . 'settlement/cost.php',
			];
			$fixtures['calculation-cost'] = [
				'class' => CalculationCostFixture::class,
				'dataFile' => static::dataDir() . 'settlement/calculation-cost.php',
			];
		}
		return $fixtures;
	}

	public static function payReceived(): array {
		return [
			static::PAY_RECEIVED => [
				'class' => PayReceivedFixture::class,
				'dataFile' => static::dataDir() . 'settlement/pay-received.php',
			],
		];
	}

	public static function summon(): array {
		return array_merge(TerytFixtureHelper::fixtures(), [
			static::SUMMON => [
				'class' => SummonFixture::class,
				'dataFile' => static::dataDir() . 'issue/summon.php',
			],
		]);
	}

}
