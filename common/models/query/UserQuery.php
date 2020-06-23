<?php

namespace common\models\query;

use Yii;
use yii\db\ActiveQuery;
use common\models\User;

/**
 * This is the ActiveQuery class for [[\common\models\User]].
 *
 * @see \common\models\User
 */
class UserQuery extends ActiveQuery {

	public function onlyByRole(array $roles, bool $common) {
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
		$this->andWhere(['id' => $ids]);
		$this->cache(60);
		return $this;
	}

	public function onlyWithBoss() {
		$this->andWhere('boss IS NOT NULL');
		return $this;
	}

	/**
	 * @return $this
	 */
	public function active() {
		$this->andWhere(['status' => User::STATUS_ACTIVE]);
		$this->andWhere(['<', '{{%user}}.created_at', time()]);

		return $this;
	}
}
