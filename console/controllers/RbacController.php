<?php

namespace console\controllers;

use common\models\user\User;
use common\rbac\OwnModelRule;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\rbac\Item;

class RbacController extends Controller {

	public array $roles = [
		User::ROLE_AGENT,
		User::ROLE_BOOKKEEPER,
		User::ROLE_CUSTOMER_SERVICE,
		User::ROLE_LAWYER,
		User::ROLE_TELEMARKETER,
		User::ROLE_CLIENT,
	];

	public array $permissions = [
		User::PERMISSION_ARCHIVE,
		User::PERMISSION_ISSUE,
		User::PERMISSION_LOGS,
		User::PERMISSION_MEET,
		User::PERMISSION_NEWS,
		User::PERMISSION_NOTE,
		User::PERMISSION_PAYS_DELAYED => [
			User::ROLE_BOOKKEEPER,
		],
		User::PERMISSION_SUMMON => [
			User::ROLE_AGENT,
		],
	];

	public function actionInit() {
		$auth = Yii::$app->authManager;
		$auth->removeAll();

		$user = $auth->createRole(User::ROLE_USER);
		$auth->add($user);

		// own model rule
		$ownModelRule = new OwnModelRule();
		$auth->add($ownModelRule);

		$manager = $auth->createRole(User::ROLE_MANAGER);
		$auth->add($manager);
		$auth->addChild($manager, $user);

		$loginToBackend = $auth->createPermission('loginToBackend');
		$auth->add($loginToBackend);
		$auth->addChild($manager, $loginToBackend);

		$admin = $auth->createRole(User::ROLE_ADMINISTRATOR);
		$auth->add($admin);
		$auth->addChild($admin, $manager);

		$roles = $this->createRoles($this->roles);
		foreach ($roles as $item) {
			$this->assignAdmin($item);
		}
		$permissions = $this->createPermissions($this->permissions);
		foreach ($permissions as $item) {
			$this->assignAdmin($item);
		}

		$auth->assign($admin, 1);

		Console::output('Success! RBAC roles has been added.');
	}

	private function createRoles(array $roles): array {
		if (empty($roles)) {
			return [];
		}
		$auth = Yii::$app->authManager;
		$items = [];
		foreach ($roles as $roleName) {
			$role = $auth->createRole($roleName);
			$auth->add($role);
			$items[] = $role;
		}
		return $items;
	}

	private function createPermissions(array $permissions): array {
		if (empty($permissions)) {
			return [];
		}
		$auth = Yii::$app->authManager;
		$items = [];
		foreach ($permissions as $permissionName => $roles) {
			if (!is_string($permissionName) && is_string($roles)) {
				$permissionName = $roles;
			}
			$permission = $auth->createPermission($permissionName);
			$auth->add($permission);
			$items[] = $permission;
			if (is_array($roles)) {
				foreach ($roles as $roleName) {
					$role = $auth->getRole($roleName);
					$auth->addChild($role, $permission);
				}
			}
		}
		return $items;
	}

	private function assignAdmin(Item $item): bool {
		$auth = Yii::$app->authManager;
		$admin = $auth->getRole(User::ROLE_ADMINISTRATOR);
		return $auth->addChild($admin, $item);
	}

}
