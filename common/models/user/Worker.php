<?php

namespace common\models\user;

use Closure;
use common\models\user\query\UserQuery;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class Worker
 *
 * {@inheritdoc}
 *
 * @property-read Worker $parent
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class Worker extends User {

	public const ROLES = [
		self::ROLE_AGENT,
		self::ROLE_TELEMARKETER,
		self::ROLE_BOOKKEEPER,
		self::ROLE_CUSTOMER_SERVICE,
		self::ROLE_LAWYER,
	];

	private static $USER_NAMES = [];

	private $selfTree;
	private static $PARENTS_MAP = [];
	private static $TREE = [];

	public function hasParent(): bool {
		return $this->boss !== null;
	}

	public function getParent() {
		return $this->hasOne(static::class, ['boss' => 'id']);
	}

	public function getParents(): array {
		return $this->getParentsQuery()->all();
	}

	public function getParentsQuery(): ActiveQuery {
		return static::find()->where(['id' => $this->getParentsIds()]);
	}

	public function getParentsIds(): array {
		if (!$this->hasParent()) {
			return [];
		}
		return static::findParents($this->id);
	}

	private static function findParents(int $userId): array {
		$ids = [];
		while (($userId = static::getBossId($userId)) !== null) {
			$ids[] = $userId;
		}
		return $ids;
	}

	private static function getBossId(int $userId): ?int {
		return static::getParentsIdsMap()[$userId] ?? null;
	}

	/**
	 * @return static[]
	 */
	public function getChildes(): array {
		return $this->getChildesQuery()->all();
	}

	public function getChildesQuery(): ActiveQuery {
		return static::find()->where(['id' => $this->getChildesIds()]);
	}

	public function getChildesIds(): array {
		$ids = [];
		$parentsIdsMap = static::getParentsIdsMap();
		foreach ($parentsIdsMap as $id => $parent) {
			if ($parent === $this->id) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	public function getAllChildes(): array {
		return $this->getAllChildesQuery()->all();
	}

	public function getAllChildesQuery(): ActiveQuery {
		return static::find()->where(['id' => $this->getAllChildesIds()]);
	}

	public function getAllChildesIds(): array {
		$selfTree = $this->getSelfTree();
		if (empty($selfTree)) {
			return [];
		}
		$ids = [];
		array_walk_recursive($selfTree, static function ($item, $key) use (&$ids) {
			if ($key === 'id') {
				$ids[] = $item;
			}
		});
		return $ids;
	}

	private static function getParentsIdsMap(): array {
		if (empty(static::$PARENTS_MAP)) {
			static::$PARENTS_MAP = array_map('intval',
				ArrayHelper::map(static::find()
					->select('id,boss')
					->onlyWithBoss()
					->asArray()
					->all(), 'id', 'boss'),
			);
		}
		return static::$PARENTS_MAP;
	}

	public function getSelfTree(): array {
		return static::getTree()[$this->id] ?? [];
	}

	public static function getTree(): array {
		if (empty(static::$TREE)) {
			$boss = static::find()
				->select('id,boss')
				->onlyWithBoss()
				->asArray()
				->all();
			static::$TREE = static::buildTree($boss, 'boss', 'id');
		}
		return static::$TREE;
	}

	private static function buildTree(array $items, string $parentKey, string $idKey): array {
		$childs = [];
		foreach ($items as &$item) {
			$childs[$item[$parentKey]][] = &$item;
		}
		unset($item);
		foreach ($items as &$item) {
			if (isset($childs[$item[$idKey]])) {
				$item['childs'] = $childs[$item[$idKey]];
			}
		}
		return $childs;
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

	public static function getSelectList(array $roles = [], bool $commonRoles = true, ?Closure $beforeAll = null): array {
		$query = static::find()
			->joinWith('userProfile')
			->with('userProfile')
			->active()
			->orderBy('user_profile.lastname');
		if (!empty($roles)) {
			$query->onlyByRoles($roles, $commonRoles);
		}
		if ($beforeAll instanceof Closure) {
			$beforeAll($query);
		}
		$query->cache(60);

		return ArrayHelper::map(
			$query->all(), 'id', 'fullName');
	}

	public static function find(): UserQuery {
		return parent::find()->workers();
	}

}
