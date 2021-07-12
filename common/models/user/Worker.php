<?php

namespace common\models\user;

use common\models\hierarchy\HierarchyModel;
use common\models\user\query\UserQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class Worker
 *
 * {@inheritdoc}
 *
 * @property-read Worker $parent
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 *
 */
class Worker extends User implements HierarchyModel {

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
		return $this->boss !== null;
	}

	public function getParent(): ActiveQuery {
		return $this->hasOne(static::class, ['id' => 'boss']);
	}

	public function getParentsQuery(): ActiveQuery {
		return static::find()->where(['id' => $this->getParentsIds()]);
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

	public function getAllChildesQuery(): ActiveQuery {
		return static::find()->where(['id' => $this->getAllChildesIds()]);
	}

	public function getAllChildesIds(): array {
		return Yii::$app
			->userHierarchy->getAllChildesIds($this->id);
	}

	public static function userName(int $id): string {
		return static::userNames()[$id];
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

	public function getParentId(): ?int {
		return $this->boss;
	}
}
