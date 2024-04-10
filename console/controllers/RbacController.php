<?php

namespace console\controllers;

use common\components\DbManager;
use common\models\user\Customer;
use common\models\user\User;
use common\models\user\Worker;
use common\rbac\OwnModelRule;
use Exception;
use Yii;
use yii\base\InvalidArgumentException;
use yii\console\Controller;
use yii\helpers\Console;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;

/**
 * Class RbacController
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class RbacController extends Controller {

	public array $roles = [
		Worker::ROLE_AGENT,
		Worker::ROLE_CO_AGENT,
		Worker::ROLE_BOOKKEEPER,
		Worker::ROLE_CUSTOMER_SERVICE,
		Worker::ROLE_LAWYER,
		Worker::ROLE_LAWYER_OFFICE,
		Worker::ROLE_LAWYER_ASSISTANT,
		Worker::ROLE_TELEMARKETER,
		Worker::ROLE_VINDICATOR,
		Customer::ROLE_CUSTOMER,
		Customer::ROLE_VICTIM,
		Customer::ROLE_SHAREHOLDER,
		Customer::ROLE_HANDICAPPED,
		User::ROLE_RECCOMENDING,
		User::ROLE_GUARDIAN,
	];

	public array $permissions = [
		User::PERMISSION_ARCHIVE,
		User::PERMISSION_ARCHIVE_DEEP,
		Worker::PERMISSION_COST => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_COST_DEBT => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_CALCULATION_TO_CREATE => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_CALCULATION_UPDATE => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_CALCULATION_PAYS => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_CALCULATION_PROBLEMS => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_ISSUE_SEARCH_WITH_SETTLEMENTS => [
			User::ROLE_BOOKKEEPER,
		],
		User::PERMISSION_EXPORT,
		User::PERMISSION_ISSUE,
		Worker::PERMISSION_ISSUE_ATTACHMENTS,
		Worker::PERMISSION_ISSUE_CLAIM,
		Worker::PERMISSION_ISSUE_CLAIM_TOTAL_SUM,
		Worker::PERMISSION_ISSUE_CREATE,
		Worker::PERMISSION_ISSUE_DELETE,
		Worker::PERMISSION_ENTITY_RESPONSIBLE_MANAGER,
		Worker::PERMISSION_ISSUE_LINK_USER,
		Worker::PERMISSION_ISSUE_NOTE_EMAIL_MESSAGE_ISSUE,
		Worker::PERMISSION_ISSUE_NOTE_EMAIL_MESSAGE_SETTLEMENT,
		Worker::PERMISSION_ISSUE_NOTE_EMAIL_MESSAGE_SUMMON,
		Worker::PERMISSION_ISSUE_SEARCH_PARENTS,
		Worker::PERMISSION_ISSUE_SHIPMENT,
		Worker::PERMISSION_ISSUE_STAGE_CHANGE,
		Worker::PERMISSION_ISSUE_STAGE_MANAGER,
		Worker::PERMISSION_ISSUE_STAT,
		Worker::PERMISSION_ISSUE_TAG_MANAGER,
		Worker::PERMISSION_ISSUE_TYPE_MANAGER,
		Worker::PERMISSION_HINT,
		Worker::PERMISSION_MESSAGE_EMAIL_ISSUE_CREATE,
		Worker::PERMISSION_MESSAGE_EMAIL_ISSUE_PAY_PAID,
		Worker::PERMISSION_MESSAGE_EMAIL_ISSUE_SETTLEMENT_CREATE,
		Worker::PERMISSION_MESSAGE_EMAIL_SUMMON_IMMINENT_DEADLINE,
		Worker::PERMISSION_MESSAGE_EMAIL_ISSUE_STAGE_CHANGE,
		User::PERMISSION_LOGS,
		User::PERMISSION_NEWS,
		User::PERMISSION_NEWS_MANAGER,
		User::PERMISSION_NOTE,
		Worker::PERMISSION_NOTE_DELETE,
		Worker::PERMISSION_NOTE_MANAGER,
		User::PERMISSION_NOTE_UPDATE,
		User::PERMISSION_NOTE_SELF,
		Worker::PERMISSION_NOTE_TEMPLATE,
		User::PERMISSION_PROVISION,
		User::PERMISSION_ISSUE_VISIBLE_NOT_SELF => [
			User::ROLE_MANAGER,
			User::ROLE_CUSTOMER_SERVICE,
		],
		Worker::PERMISSION_PAY => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_PAYS_DELAYED => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_PAY_PART_PAYED => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_PAY_RECEIVED => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_PAY_PAID => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_PAY_UPDATE => [
			Worker::ROLE_BOOKKEEPER,
			Worker::ROLE_AGENT,
		],
		Worker::PERMISSION_PAY_ALL_PAID => [
			Worker::ROLE_BOOKKEEPER,
		],
		Worker::PERMISSION_POTENTIAL_CLIENT,
		Worker::PERMISSION_SUMMON => [
			Worker::ROLE_AGENT,
		],
		Worker::PERMISSION_SUMMON_MANAGER,
		Worker::PERMISSION_SUMMON_DOC_MANAGER,
		Worker::PERMISSION_SUMMON_CREATE,
		Worker::PERMISSION_SMS,
		Worker::PERMISSION_MULTIPLE_SMS,
		User::PERMISSION_USER_TRAITS,
		Worker::PERMISSION_WORKERS,
		Worker::PERMISSION_WORKERS_HIERARCHY,
		Worker::PERMISSION_LEAD,
		Worker::PERMISSION_LEAD_MANAGER,
		Worker::PERMISSION_LEAD_DELETE,
		Worker::PERMISSION_LEAD_DIALER,
		Worker::PERMISSION_LEAD_DIALER_MANAGER,
		Worker::PERMISSION_LEAD_DUPLICATE,
		Worker::PERMISSION_LEAD_IMPORT,
		Worker::PERMISSION_LEAD_UPDATE_MULTIPLE,
		Worker::PERMISSION_LEAD_MARKET,
		Worker::PERMISSION_LEAD_SMS_WELCOME,
		Worker::PERMISSION_LEAD_STATUS,
		Worker::PERMISSION_MESSAGE_TEMPLATE,
		Worker::PERMISSION_PROVISION_CHILDREN_VISIBLE,
		Worker::PERMISSION_SETTLEMENT_ADMINISTRATIVE_CREATE,
		Worker::PERMISSION_SETTLEMENT_DELETE_NOT_SELF,
		Worker::PERMISSION_CREDIT_ANALYZE,
		Worker::PERMISSION_COURT,
		Worker::PERMISSION_LAWSUIT,
		Worker::ROLE_AUDITOR,
	];

	public function actionAddPermissionToWorkers(string $name, array $assignments): void {
		$auth = Yii::$app->authManager;
		$permission = $auth->getPermission($name);
		if ($permission === null) {
			Console::output('Not Find Permission: ' . $name);
			return;
		}

		foreach (Worker::getAssignmentIds($assignments) as $id) {
			try {
				$auth->assign($permission, $id);
			} catch (\yii\base\Exception $exception) {
				Console::output($exception->getMessage());
			}
		}
	}

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

	public function actionCreateNotExist(): void {
		$auth = Yii::$app->authManager;
		$roles = $auth->getRoles();
		$newRoles = [];
		foreach ($this->roles as $role) {
			if (!isset($roles[$role])) {
				Console::output('New Role to Add: ' . $role);
				$newRoles[] = $role;
			}
		}

		$roles = $this->createRoles($newRoles);
		foreach ($roles as $role) {
			$this->assignAdmin($role);
		}

		$permissions = $auth->getPermissions();
		$newPermissions = [];

		foreach ($this->permissions as $permissionName => $roles) {
			if (!is_string($permissionName) && is_string($roles)) {
				$permissionName = $roles;
			}
			if (!isset($permissions[$permissionName])) {
				Console::output('New Permission to Add: ' . $permissionName);
				$newPermissions[$permissionName] = $roles;
			}
		}

		$permissions = $this->createPermissions($newPermissions);
		foreach ($permissions as $permission) {
			$this->assignAdmin($permission);
		}
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

	public function actionRemoveRole(string $name): void {
		$auth = Yii::$app->authManager;
		$role = $auth->getRole($name);
		if ($role && $auth->remove($role)) {
			Console::output('Success remove Role: ' . $name);
		}
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

	public function actionRemovePermission(string $name): void {
		$auth = Yii::$app->authManager;
		$permission = $auth->getPermission($name);
		if ($permission && $auth->remove($permission)) {
			Console::output('Success remove Permission: ' . $name);
		}
	}

	public function actionAddChildRolePermission(string $roleName, string $permissionName): void {
		$auth = Yii::$app->authManager;
		$role = $auth->getRole($roleName);
		if ($role === null) {
			Console::output("Role with name: $roleName not found.");
			return;
		}
		$permission = $auth->getPermission($permissionName);
		if ($permission === null) {
			Console::output("Permission with name: $permissionName not found.");
			return;
		}
		$auth->addChild($role, $permission);
		Console::output("Success add permission: $permissionName as child: $roleName.");
	}

	public function actionCopy(int $type, string $from, string $to): void {
		$types = [Item::TYPE_ROLE, Item::TYPE_PERMISSION];
		if (!in_array($type, $types, true)) {
			throw new InvalidArgumentException('Invalid Rbac Item $type.');
		}
		$auth = Yii::$app->authManager;
		$ids = $auth->getUserIdsByRole($from);
		$item = $type === Item::TYPE_ROLE
			? new Role()
			: new Permission();
		$item->name = $to;
		$count = 0;
		foreach ($ids as $id) {
			try {
				$auth->assign($item, $id);
				$count++;
			} catch (Exception $exception) {
				Console::output($exception->getMessage());
			}
		}
		Console::output('Copy rbac items: ' . $count);
	}

	public function actionClearAssignments(): void {
		$auth = Yii::$app->authManager;
		if ($auth instanceof DbManager) {
			$count = Yii::$app->db->createCommand()
				->delete($auth->assignmentTable, [
					'NOT IN', 'user_id', User::find()->select('id')->column(),
				])
				->execute();

			Console::output('Delete Assignmnets: ' . $count);
		}
	}

	public function actionAssignPermission(string $name, array $usersIds): void {
		$auth = Yii::$app->authManager;
		$permission = $auth->getPermission($name);
		if ($permission) {
			$count = 0;
			foreach ($usersIds as $id) {
				$auth->assign($permission, $id);
				$count++;
			}
			Console::output('Assign for Users: ' . $count);
		}
	}

	public function actionRevokePermission(string $name): void {
		$auth = Yii::$app->authManager;
		$permission = $auth->getPermission($name);
		if ($permission) {
			$count = 0;
			$users = User::getAssignmentIds([User::PERMISSION_WORKERS]);
			foreach ($users as $id) {
				$auth->revoke($permission, $id);
				$count++;
			}
			Console::output('Revoke for Users: ' . $count);
		}
	}
}
