<?php

namespace common\models\user;

use common\models\relation\ActiveHierarchy;
use common\models\user\query\UserQuery;
use Yii;

/**
 * Class Worker
 *
 * {@inheritdoc}
 *
 * @property-read Worker $parent
 * @property-read Worker[] $parents
 * @property-read Worker[] $childes
 * @property-read Worker[] $allChildes
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 *
 */
class Worker extends User implements ActiveHierarchy {

	public const ROLES = [
		self::ROLE_AGENT,
		self::ROLE_TELEMARKETER,
		self::ROLE_BOOKKEEPER,
		self::ROLE_CUSTOMER_SERVICE,
		self::ROLE_LAWYER,
		self::ROLE_MANAGER,
	];

	private static $USER_NAMES = [];

	public function hasParent(): bool {
		return $this->getParentId() !== null;
	}

	public function getParent(): UserQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(static::class, ['id' => 'parentId']);
	}

	public function getParents(): UserQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(static::class, ['id' => 'parentsIds']);
	}

	public function getChildes(): UserQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(static::class, ['id' => 'childesIds']);
	}

	public function getAllChildes(): UserQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(static::class, ['id' => 'allChildesIds']);
	}

	public function getParentId(): ?int {
		return Yii::$app->userHierarchy->getParent($this->id);
	}

	public function getParentsIds(): array {
		if (!$this->hasParent()) {
			return [];
		}
		return Yii::$app->userHierarchy->getParentsIds($this->id);
	}

	public function getChildesIds(): array {
		return Yii::$app->userHierarchy->getChildesIds($this->id);
	}

	public function getAllChildesIds(): array {
		return Yii::$app->userHierarchy->getAllChildesIds($this->id);
	}

	public static function userName(int $id): string {
		return static::userNames()[$id] ?? '';
	}

	private static function userNames(): array {
		if (empty(static::$USER_NAMES)) {
			static::$USER_NAMES = static::find()
				->select('username')
				->active()
				->asArray()
				->indexBy('id')
				->column();
		}
		return static::$USER_NAMES;
	}

	public static function find(): UserQuery {
		return parent::find()->workers();
	}

}
