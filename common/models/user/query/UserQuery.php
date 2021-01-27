<?php

namespace common\models\user\query;

use common\components\DbManager;
use common\models\user\Customer;
use common\models\user\User;
use common\models\user\Worker;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\conditions\LikeCondition;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[\common\models\User]].
 *
 * @see User
 */
class UserQuery extends ActiveQuery {

	private bool $isAssignmentJoin = false;

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

	public function onlyByRoles(array $roles, bool $common): self {
		$auth = Yii::$app->authManager;
		$ids = [];
		$i = 0;
		//@todo add test for them.
		if (!$auth instanceof DbManager) {
			if (!$this->isAssignmentJoin) {
				$this->isAssignmentJoin = true;
				$this->leftJoin('auth_assignment', 'user.id = auth_assignment.user_id');
			}
			$columnName = $auth->assignmentTable . '.item_name';
			if ($common) {
				foreach ($roles as $role) {
					$this->andWhere([$columnName => $role]);
				}
			} else {
				$this->andWhere([$columnName => $roles]);
			}
		} else {
			foreach ($roles as $role) {
				$userIds = $auth->getUserIdsByRole($role);
				if ($common && $i > 0) {
					$ids = array_intersect($ids, $userIds);
				} else {
					$ids = array_merge($ids, $userIds);
				}
				$i++;
			}
			$this->andWhere([User::tableName() . '.id' => $ids]);
		}

		return $this;
	}

	public function onlyWithBoss(): self {
		$this->andWhere('boss IS NOT NULL');
		return $this;
	}

	public function active(): self {
		$this->andWhere(['status' => User::STATUS_ACTIVE]);
		return $this;
	}

	public function withPhoneNumber(string $phone): self {
		$likePhone = $this->preparePhoneLikeCondition('user_profile.phone', $phone);
		$likePhone2 = $this->preparePhoneLikeCondition('user_profile.phone_2', $phone);
		$this->andWhere($likePhone)->orWhere($likePhone2);
		return $this;
	}

	private function preparePhoneLikeCondition(string $column, $phone): LikeCondition {
		$phoneReplaced = str_replace([' ', '-'], [''], $phone);

		$applySpaceReplace = new Expression(
			'REPLACE(' . $column . ', " ", "")'
		);
		$applyDashReplace = new Expression(
			'REPLACE(' . $applySpaceReplace . ', "-", "")'
		);
		return new LikeCondition($applyDashReplace, 'LIKE', $phoneReplaced);
	}

}
