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

	public function onlyByRole(array $roles) {
		$ids = [];
		foreach ($roles as $role) {
			$ids = array_merge($ids, Yii::$app->authManager->getUserIdsByRole($role));
		}
		$this->andWhere(['id' => $ids]);
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
