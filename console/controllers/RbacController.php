<?php

namespace console\controllers;

use common\models\user\Customer;
use common\models\user\User;
use common\models\user\Worker;
use common\rbac\OwnModelRule;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\rbac\Item;

/**
 * Class RbacController
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class RbacController extends Controller {

	public array $roles = [
		Worker::ROLE_AGENT,
		Worker::ROLE_BOOKKEEPER,
		Worker::ROLE_CUSTOMER_SERVICE,
		Worker::ROLE_LAWYER,
		Worker::ROLE_TELEMARKETER,
		Customer::ROLE_CUSTOMER,
		Customer::ROLE_VICTIM,
		Customer::ROLE_SHAREHOLDER,
		Customer::ROLE_HANDICAPPED,
	];

	public array $permissions = [
		User::PERMISSION_ARCHIVE,
		User::PERMISSION_COST => [
			User::ROLE_BOOKKEEPER,
		],
		User::PERMISSION_CALCULATION_TO_CREATE => [
			User::ROLE_BOOKKEEPER,
		],
		User::PERMISSION_CALCULATION_PAYS => [
			User::ROLE_BOOKKEEPER,
		],
		User::PERMISSION_CALCULATION_PROBLEMS => [
			User::ROLE_BOOKKEEPER,
		],
		User::PERMISSION_EXPORT,
		User::PERMISSION_ISSUE,
		User::PERMISSION_LOGS,
		User::PERMISSION_MEET,
		User::PERMISSION_NEWS,
		User::PERMISSION_NOTE,
		User::PERMISSION_PROVISION,
		User::PERMISSION_PAY => [
			User::ROLE_BOOKKEEPER,
		],
		User::PERMISSION_PAYS_DELAYED => [
			User::ROLE_BOOKKEEPER,
		],
		User::PERMISSION_PAY_RECEIVED => [
			User::ROLE_BOOKKEEPER,
		],
		User::PERMISSION_SUMMON => [
			User::ROLE_AGENT,
		],
	];

	public function actionInit(): void {
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
		if (!YII_ENV_TEST) {
			$auth->assign($admin, 1);
		}

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

	public function actionAddRole(string $name, bool $admin = true): void {
		$auth = Yii::$app->authManager;
		$role = $auth->createRole($name);

		$auth->add($role);
		if ($admin) {
			$this->assignAdmin($role);
		}

		Console::output('Success add role: ' . $name);
	}

	public function actionAddPermission(string $name, bool $admin = true): void {
		$auth = Yii::$app->authManager;
		$permission = $auth->createPermission($name);
		$auth->add($permission);
		if ($admin) {
			$this->assignAdmin($permission);
		}
		Console::output('Success add permission: ' . $name);
	}
}
