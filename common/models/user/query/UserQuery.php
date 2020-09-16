<?php

namespace common\models\user\query;

use common\models\user\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\User]].
 *
 * @see User
 */
class UserQuery extends ActiveQuery {

	public function workers(): self {
		return $this->onlyByRoles(User::WORKERS_ROLES, false);
	}

	public function customers(): self {
		return $this->onlyByRoles(User::CUSTOMERS_ROLES, false);
	}

	public function onlyByRoles(array $roles, bool $common): self {
		$auth = Yii::$app->authManager;
		$ids = [];
		$i = 0;
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
		return $this;
	}

	public function onlyWithBoss(): self {
		$this->andWhere('boss IS NOT NULL');
		return $this;
	}

	public function active(): self {
		$this->andWhere(['status' => User::STATUS_ACTIVE]);
		$this->andWhere(['<', '{{%user}}.created_at', time()]);

		return $this;
	}
}
