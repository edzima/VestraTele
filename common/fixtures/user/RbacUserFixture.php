<?php

namespace common\fixtures\user;

use common\fixtures\UserFixture;
use Yii;

class RbacUserFixture extends UserFixture {

	public array $roles = [];
	public array $permissions = [];

	public function load() {
		parent::load();
		foreach ($this->data as $attributes) {
			$id = $attributes['id'] ?? null;

			if ($id) {
				Yii::$app->authManager->revokeAll($id);

				foreach ($this->roles as $role) {
					Yii::$app->authManager->assign(\Yii::$app->authManager->getRole($role), $id);
				}
				foreach ($this->permissions as $permission) {
					Yii::$app->authManager->assign(\Yii::$app->authManager->getPermission($permission), $id);
				}
			}
		}
	}

	public function unload() {
		foreach ($this->data as $attributes) {
			$id = $attributes['id'] ?? null;
			if ($id) {
				foreach ($this->roles as $role) {
					Yii::$app->authManager->revoke(\Yii::$app->authManager->getRole($role), $id);
				}
				foreach ($this->permissions as $permission) {
					Yii::$app->authManager->revoke(\Yii::$app->authManager->getPermission($permission), $id);
				}
			}
		}
		parent::unload();
	}
}
