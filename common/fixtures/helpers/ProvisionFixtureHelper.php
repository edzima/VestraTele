<?php

namespace common\fixtures\helpers;

use common\fixtures\provision\ProvisionFixture;
use common\fixtures\provision\ProvisionTypeFixture;
use common\fixtures\provision\ProvisionUserFixture;
use common\models\provision\IssueProvisionType;
use Yii;

class ProvisionFixtureHelper {

	public const PROVISION = 'provision';
	public const TYPE = 'provision-type';
	public const USER = 'provision-user';

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/provision/');
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
				'dataFile' => static::dataDir() . 'provision.php',
			],
		];
	}

	public static function type(): array {
		return [
			static::TYPE => [
				'class' => ProvisionTypeFixture::class,
				'dataFile' => static::dataDir() . 'type.php',
			],
		];
	}

	public static function issueType(): array {
		return [
			static::TYPE => [
				'class' => ProvisionTypeFixture::class,
				'modelClass' => IssueProvisionType::class,
				'dataFile' => static::dataDir() . 'type.php',
			],
		];
	}

	public static function user(): array {
		return [
			static::USER => [
				'class' => ProvisionUserFixture::class,
				'dataFile' => static::dataDir() . 'user.php',
			],
		];
	}
}
