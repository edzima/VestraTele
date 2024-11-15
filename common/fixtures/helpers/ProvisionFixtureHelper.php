<?php

namespace common\fixtures\helpers;

use common\fixtures\provision\ProvisionFixture;
use common\fixtures\provision\ProvisionTypeFixture;
use common\fixtures\provision\ProvisionUserFixture;
use common\models\provision\IssueProvisionType;
use common\models\provision\Provision;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use Yii;

class ProvisionFixtureHelper extends BaseFixtureHelper {

	public const TYPE_AGENT_PERCENT_25 = 1;

	public const TYPE_AGENT_CONST = 100;

	protected const DEFAULT_TYPE_ID = self::TYPE_AGENT_PERCENT_25;
	protected const DEFAULT_USER_ID = UserFixtureHelper::AGENT_PETER_NOWAK;

	public const PROVISION = 'provision';
	public const TYPE = 'provision-type';
	public const USER = 'provision-user';

	public static function getDefaultDataDirPath(): string {
		return Yii::getAlias('@common/tests/_data/provision/');
	}

	public function grabProvision(string $index): Provision {
		return $this->tester->grabFixture(static::PROVISION, $index);
	}

	public static function all(): array {
		return array_merge(
			static::provision(),
			static::user(),
			static::type()
		);
	}

	public static function provision(): array {
		return [
			static::PROVISION => [
				'class' => ProvisionFixture::class,
				'dataFile' => static::getDataDirPath() . 'provision.php',
			],
		];
	}

	public static function type(): array {
		return [
			static::TYPE => [
				'class' => ProvisionTypeFixture::class,
				'dataFile' => static::getDataDirPath() . 'type.php',
			],
		];
	}

	public static function issueType(): array {
		return [
			static::TYPE => [
				'class' => ProvisionTypeFixture::class,
				'modelClass' => IssueProvisionType::class,
				'dataFile' => static::getDataDirPath() . 'type.php',
			],
		];
	}

	public static function user(): array {
		return [
			static::USER => [
				'class' => ProvisionUserFixture::class,
				'dataFile' => static::getDataDirPath() . 'user.php',
			],
		];
	}

	public function haveProvisionUser($value, array $attributes = []): int {
		$attributes['value'] = $value;
		return $this->tester->haveRecord(ProvisionUser::class, $this->defaultProvisionUserAttributes($attributes));
	}

	public function defaultProvisionUserAttributes(array $attributes): array {
		if (!isset($attributes['from_user_id'])) {
			$attributes['from_user_id'] = static::DEFAULT_USER_ID;
		}
		if (!isset($attributes['to_user_id'])) {
			$attributes['to_user_id'] = static::DEFAULT_USER_ID;
		}
		if (!isset($attributes['type_id'])) {
			$attributes['type_id'] = static::DEFAULT_TYPE_ID;
		}
		return $attributes;
	}

	public function haveProvision($value, array $attributes = []): int {
		$attributes['value'] = $value;
		return $this->tester->haveRecord(Provision::class, $this->defaultProvisionAttributes($attributes));
	}

	public function defaultProvisionAttributes(array $attributes): array {
		if (!isset($attributes['from_user_id'])) {
			$attributes['from_user_id'] = static::DEFAULT_USER_ID;
		}
		if (!isset($attributes['to_user_id'])) {
			$attributes['to_user_id'] = static::DEFAULT_USER_ID;
		}
		if (!isset($attributes['pay_id'])) {
			$attributes['pay_id'] = 1;
		}
		if (!isset($attributes['type_id'])) {
			$attributes['type_id'] = static::DEFAULT_TYPE_ID;
		}
		return $attributes;
	}

	/**
	 * @param string $index
	 * @return ProvisionType|IssueProvisionType
	 */
	public function grabType(string $index = 'agent-percent-25'): ProvisionType {
		return $this->tester->grabFixture(static::TYPE, $index);
	}
}
