<?php

namespace common\models\user\query;

use common\components\DbManager;
use common\models\user\Customer;
use common\models\user\User;
use common\models\user\Worker;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\User]].
 *
 * @see User
 */
class UserQuery extends ActiveQuery {

	protected static $assignmentJoinCount = 0;

	public function orderByLastname(): self {
		$this->joinWith('userProfile');
		$this->addOrderBy(['user_profile.lastname' => SORT_ASC]);
		return $this;
	}

	public function workers(): self {
		return $this->onlyAssignments(Worker::ROLES, false);
	}

	public function customers(): self {
		return $this->onlyAssignments(Customer::ROLES, false);
	}

	public function onlyAssignments(array $names, bool $common): self {
		if (empty($names)) {
			return $this;
		}
		if (count($names) === 1) {
			$common = false;
		}
		$auth = Yii::$app->authManager;
		if ($auth === null) {
			throw new InvalidConfigException('authManager must be set.');
		}
		[$table, $alias] = $this->getTableNameAndAlias();

		if ($auth instanceof DbManager) {
			static::$assignmentJoinCount++;
			$assignmentAlias = 'AT_' . static::$assignmentJoinCount;
			$this->leftJoin($auth->assignmentTable . ' ' . $assignmentAlias, "$alias.id = $assignmentAlias.user_id");
			$this->andWhere([$assignmentAlias . '.item_name' => $names]);
			if ($common) {
				$this->groupBy($alias . '.id');
				$this->having('COUNT(' . $assignmentAlias . '.item_name) = ' . count($names));
			}
			$this->distinct();
			return $this;
		}
		$assignmentsIds = [];
		foreach ($names as $name) {
			$assignmentsIds[] = $auth->getUserIdsByRole($name);
		}
		$this->andWhere([$alias . '.id' => $this->prepareIds($assignmentsIds, $common)]);

		return $this;
	}

	private function prepareIds(array $assignmentsIds, bool $common): array {
		if (!$common) {
			return array_unique(array_merge([], ...$assignmentsIds));
		}
		$i = 0;
		$ids = [];
		foreach ($assignmentsIds as $nameIds) {
			if ($i === 0) {
				$ids = $nameIds;
			} else {
				$ids = array_intersect($ids, $nameIds);
			}
			$i++;
		}
		return $ids;
	}

	public function active(): self {
		$this->andWhere(['status' => User::STATUS_ACTIVE]);
		return $this;
	}

	/**
	 * @inheritdoc
	 * @return User[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return User|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}
}
