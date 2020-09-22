<?php

use yii\db\Migration;
use yii\rbac\DbManager;
use yii\rbac\Item;

/**
 * Class m200922_184132_change_auth_roles_to_permissions
 */
class m200922_184132_change_auth_roles_to_permissions extends Migration {

	private const ROLES = ['archive', 'issue', 'logs', 'meet', 'news', 'note', 'pays.delayed'];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$auth = Yii::$app->authManager;
		if ($auth instanceof DbManager) {
			$this->update($auth->itemTable, ['name' => 'pays.delayed'], ['name' => 'book_keeper_delayed']);
			$this->update($auth->itemTable, ['type' => Item::TYPE_PERMISSION], ['name' => static::ROLES]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$auth = Yii::$app->authManager;
		if ($auth instanceof DbManager) {
			$this->update($auth->itemTable, ['type' => Item::TYPE_ROLE], ['name' => static::ROLES]);
			$this->update($auth->itemTable, ['name' => 'book_keeper_delayed'], ['name' => 'pays.delayed']);
		}
	}

}
