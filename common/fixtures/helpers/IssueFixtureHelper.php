<?php

namespace common\fixtures\helpers;

use common\fixtures\AddressFixture;
use common\fixtures\issue\EntityResponsibleFixture;
use common\fixtures\issue\IssueFixture;
use common\fixtures\issue\IssueRelationFixture;
use common\fixtures\issue\IssueUserFixture;
use common\fixtures\issue\NoteFixture;
use common\fixtures\issue\ShipmentsPolskaPocztaFixture;
use common\fixtures\issue\StageFixture;
use common\fixtures\issue\StageTypesFixtures;
use common\fixtures\issue\SummonDocFixture;
use common\fixtures\issue\SummonFixture;
use common\fixtures\issue\SummonTypeFixture;
use common\fixtures\issue\TypeFixture;
use common\fixtures\user\UserAddressFixture;
use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use common\models\user\User;
use common\models\user\Worker;
use Yii;
use yii\base\InvalidConfigException;

class IssueFixtureHelper extends BaseFixtureHelper {

	public const DEFAULT_AGENT_ID = UserFixtureHelper::AGENT_PETER_NOWAK;
	public const DEFAULT_CUSTOMER_ID = UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID;

	public const AGENT = 'issue.agent';
	public const CUSTOMER = 'issue.customer';
	public const LAWYER = 'issue.lawyer';
	public const TELEMARKETER = 'issue.telemarketer';

	public const ISSUE = 'issue';

	public const SUMMON = 'issue.summon';
	public const SUMMON_DOC = 'issue.summon_doc';
	public const SUMMON_TYPE = 'issue.summon_type';

	private const TYPE = 'issue.type';
	private const STAGE = 'issue.stage';
	public const NOTE = 'issue.note';
	private const LINKED_ISSUES = 'issue.linked';

	public static function linkedIssues(): array {
		return [
			static::LINKED_ISSUES => [
				'class' => IssueRelationFixture::class,
				'dataFile' => static::getDataDirPath() . 'relation.php',
			],
		];
	}

	public function grabIssue($index): IssueInterface {
		return $this->tester->grabFixture(static::ISSUE, $index);
	}

	public function haveIssue(array $attributes = [], bool $withAgentAndCustomer = true): int {
		$agentId = ArrayHelper::remove($attributes, 'agent_id', static::DEFAULT_AGENT_ID);
		$customerId = ArrayHelper::remove($attributes, 'customer_id', static::DEFAULT_CUSTOMER_ID);

		if (!isset($attributes['stage_id'])) {
			$attributes['stage_id'] = 1;
		}
		if (!isset($attributes['type_id'])) {
			$attributes['type_id'] = 1;
		}
		if (!isset($attributes['entity_responsible_id'])) {
			$attributes['entity_responsible_id'] = 1;
		}

		$id = $this->tester->haveRecord(Issue::class, $attributes);
		if ($withAgentAndCustomer) {
			$this->tester->haveRecord(IssueUser::class, [
				'user_id' => $agentId,
				'issue_id' => $id,
				'type' => IssueUser::TYPE_AGENT,
			]);
			$this->tester->haveRecord(IssueUser::class, [
				'user_id' => $customerId,
				'issue_id' => $id,
				'type' => IssueUser::TYPE_CUSTOMER,
			]);
		}

		return $id;
	}

	public static function dataDir(string $path = null): string {
		return $path ? $path : Yii::getAlias('@common/tests/_data/');
	}

	protected static function getDefaultDataDirPath(): string {
		return Yii::getAlias('@common/tests/_data/issue/');
	}

	public static function issue($dataDirPath = null): array {
		return [
			static::ISSUE => [
				'class' => IssueFixture::class,
				'dataFile' => static::getDataDirPath($dataDirPath) . 'issue.php',
			],
		];
	}

	public static function fixtures(bool $userProfiles = false): array {
		return array_merge(
			static::issue(),
			static::entityResponsible(),
			static::stageAndTypesFixtures(),
			static::users($userProfiles),
		);
	}

	public static function customerAddress(): array {
		return [
			'issue.customer.address' => [
				'class' => UserAddressFixture::class,
				'dataFile' => BaseFixtureHelper::getDataDirPath() . '/user/customer_address.php',

			],
			'address' => [
				'class' => AddressFixture::class,
				'dataFile' => BaseFixtureHelper::getDataDirPath() . 'address.php',
			],
		];
	}

	public static function entityResponsible(): array {
		return [
			'entity' => [
				'class' => EntityResponsibleFixture::class,
				'dataFile' => static::dataDir() . 'issue/entity_responsible.php',
			],
		];
	}

	public static function note(): array {
		return [
			static::NOTE => [
				'class' => NoteFixture::class,
				'dataFile' => static::getDataDirPath() . 'note.php',

			],
		];
	}

	public static function shipmetsPocztaPolska(): array {
		return [
			'shipments-poczta-polska' => [
				'class' => ShipmentsPolskaPocztaFixture::class,
				'dataFile' => static::getDataDirPath() . 'shipments-poczta-polska.php',

			],
		];
	}

	public static function stageAndTypesFixtures(): array {
		return array_merge([
			'stage-types' => [
				'class' => StageTypesFixtures::class,
				'dataFile' => static::dataDir() . 'issue/stage_types.php',
			],
		],
			static::stages(),
			static::types()
		);
	}

	public static function stages(): array {
		return [
			static::STAGE => [
				'class' => StageFixture::class,
				'dataFile' => static::dataDir() . 'issue/stage.php',
			],
		];
	}

	public static function types(): array {
		return [
			static::TYPE => [
				'class' => TypeFixture::class,
				'dataFile' => static::dataDir() . 'issue/type.php',
			],
		];
	}

	public static function agent(bool $withProfile = false): array {
		return array_merge(
			[
				static::AGENT => UserFixtureHelper::agent(),
			],
			static::issueUsers(),
			$withProfile ? UserFixtureHelper::profile(UserFixtureHelper::WORKER_AGENT) : []
		);
	}

	public static function customer(bool $withProfile = false): array {
		return array_merge(
			[
				static::CUSTOMER => UserFixtureHelper::customer(),
			],
			static::issueUsers(),
			$withProfile ? UserFixtureHelper::profile(UserFixtureHelper::CUSTOMER) : []
		);
	}

	public static function issueUsers(): array {
		return [
			'issue-user' => [
				'class' => IssueUserFixture::class,
				'dataFile' => static::dataDir() . 'issue/users.php',
			],
		];
	}

	public static function users(bool $profiles = false): array {
		$users = [
			static::AGENT => UserFixtureHelper::agent(),
			static::CUSTOMER => UserFixtureHelper::customer(),
			static::LAWYER => UserFixtureHelper::lawyer(),
			static::TELEMARKETER => UserFixtureHelper::telemarketer(),
		];
		foreach ($users as &$user) {
			UserFixtureHelper::addPermission($user, User::PERMISSION_ISSUE);
		}
		return array_merge(
			$users,
			$profiles ? UserFixtureHelper::profiles() : UserFixtureHelper::profile(UserFixtureHelper::CUSTOMER),
			static::issueUsers()
		);
	}

	public static function summon(): array {

		return array_merge(TerytFixtureHelper::fixtures(),
			[
				static::SUMMON => [
					'class' => SummonFixture::class,
					'dataFile' => static::dataDir() . 'issue/summon.php',
				],
				static::SUMMON_TYPE => [
					'class' => SummonTypeFixture::class,
					'dataFile' => static::dataDir() . 'issue/summon_type.php',
				],
				static::SUMMON_DOC => [
					'class' => SummonDocFixture::class,
					'dataFile' => static::dataDir() . 'issue/summon_doc.php',
				],
			]);
	}

	public static function accessTypesToIssuePermission(array $ids = []): void {
		if (empty($ids)) {
			$ids = array_keys(IssueType::getTypes());
			if (empty($ids)) {
				throw new InvalidConfigException('issue type must be exist');
			}
		}
		foreach ($ids as $id) {
			Yii::$app->issueTypeUser->ensurePermission($id);
			Yii::$app->issueTypeUser->addChild(
				Yii::$app->issueTypeUser->auth
					->getPermission(Worker::PERMISSION_ISSUE),
				$id
			);
		}
	}

	public static function accessUserTypes(string $userID, array $ids = []): void {
		if (empty($ids)) {
			$ids = array_keys(IssueType::getTypes());
			if (empty($ids)) {
				throw new InvalidConfigException('issue type must be exist');
			}
		}
		foreach ($ids as $id) {
			Yii::$app->issueTypeUser->ensurePermission($id);
			$permissionName = Yii::$app->issueTypeUser->getPermissionName($id);
			$manager = Yii::$app->issueTypeUser->auth;
			if (!$manager->checkAccess($userID, $permissionName)) {
				$manager->assign(
					$manager->getPermission($permissionName),
					$userID
				);
			}
		}
	}

}
