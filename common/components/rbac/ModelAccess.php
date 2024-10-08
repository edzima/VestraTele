<?php

namespace common\components\rbac;

use ReflectionClass;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\rbac\Assignment;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;

class ModelAccess extends Component {

	public const APP_BASIC = 'basic';
	public const APP_FRONTEND = 'frontend';
	public const APP_BACKEND = 'admin';
	public const APP_ADVANCED = [
		self::APP_BACKEND,
		self::APP_FRONTEND,
	];

	public array $availableApps = self::APP_ADVANCED;

	public const ACTION_INDEX = 'index';
	public const ACTION_VIEW = 'view';
	public const ACTION_CREATE = 'create';
	public const ACTION_UPDATE = 'update';
	public const ACTION_DELETE = 'delete';
	public ?string $modelId = null;

	public function getActions(): array {
		return [
			static::ACTION_INDEX,
			static::ACTION_VIEW,
			static::ACTION_CREATE,
			static::ACTION_UPDATE,
			static::ACTION_DELETE,
		];
	}

	public string $nameSeparator = ':';

	public string $managerRolePrefix = 'manager';

	public ?string $modelName = null;

	public string $action = self::ACTION_INDEX;

	protected string $app = self::APP_FRONTEND;

	/**
	 * @var string|array|ParentsManagerInterface
	 */
	public $auth = 'authManager';

//	public ?Closure $findModel = null;

//	/**
//	 * @var string|ActiveRecordInterface
//	 */
	public string $modelClass;

	public array $availableParentRoles = [];
	public array $availableParentPermissions = [];

	public function init(): void {
		parent::init();
		$this->auth = Instance::ensure($this->auth, ParentsManagerInterface::class);
		if ($this->modelName === null) {
			$this->modelName = (new ReflectionClass($this->modelClass))->getShortName();
		}
	}

	public function hasAccess(string|int $userId): bool {
		return $this->auth->checkAccess($userId, $this->getPermissionName());
	}

	public function assign(string|int $userId, bool $manager = false): Assignment {
		if ($manager) {
			return $this->auth->assign($this->getManagerRole(), $userId);
		}

		return $this->auth->assign($this->getPermission(), $userId);
	}

	public function setAction(string $action): self {
		$this->action = $action;
		return $this;
	}

	public function setApp(string $name): void {
		if (!empty($this->availableApps) && in_array($name, $this->availableApps)) {
			throw new InvalidConfigException("App: $name ' not is allowed");
		}
		$this->app = $name;
	}

	public function removeFromAllParents(): int {
		return $this->auth->removeChildFromParents($this->getPermissionName());
	}

	public function addToRoles(array $names): int {
		$count = 0;
		foreach ($names as $name) {
			$item = $this->auth->getRole($name);
			if ($item && $this->addToParent($item)) {
				$count++;
			}
		}
		return $count;
	}

	public function addToPermissions(array $names): int {
		$count = 0;
		foreach ($names as $name) {
			$item = $this->auth->getPermission($name);
			if ($item && $this->addToParent($item)) {
				$count++;
			}
		}
		return $count;
	}

	protected function addToParent(Item $parent): bool {
		$permission = $this->getPermission();
		if (!$permission
			|| $this->auth->hasChild($parent, $permission)) {
			return false;
		}
		return $this->auth->addChild($parent, $permission);
	}

	public function setPermissionName(string $name): self {
		$this->permissionName = $name;
		return $this;
	}

	protected function getPermission(): ?Permission {
		return $this->auth->getPermission($this->getPermissionName());
	}

	public function getParentsRoles(): array {
		return $this->auth->getParentsRoles($this->getPermissionName());
	}

	public function getParentsPermissions(): array {
		return $this->auth->getParentsPermissions($this->getPermissionName());
	}

	protected function getPermissionName(): string {
		$parts = [
			$this->app,
			$this->modelName,
			$this->action,
		];
		if (!empty($this->modelId)) {
			$parts[] = $this->modelId;
		}
		return implode($this->nameSeparator, $parts);
	}

	protected function getRoleName(): string {
		$parts = [
			$this->app,
			$this->managerRolePrefix,
			$this->modelName,
		];
		return implode($this->nameSeparator, $parts);
	}



//	protected function findModel($condition): ?ActiveRecordInterface {
//		if ($this->findModel) {
//			return call_user_func($this->findModel, $condition);
//		}
//		return $this->modelClass::findOne($condition);
//	}
	public function getManagerRole(): Role {
		$role = $this->auth->getRole($this->getRoleName());
		if ($role === null) {
			$role = $this->createManagerRole();
			$this->auth->add($role);
		}
		return $role;
	}

	protected function createManagerRole(): Role {
		$role = new Role();
		$role->name = $this->getRoleName();
		$role->description = Yii::t('rbac', 'Manager: {modelName}', [
			'modelName' => $this->modelName,
		]);
		return $role;
	}

	public function getAvailableParentRolesNames(): array {
		$names = [];
		foreach ($this->availableParentRoles as $role) {
			$item = $this->auth->getRole($role);
			if ($item !== null) {
				$names[$item->name] = Yii::t('rbac', $item->name);
			}
		}
		return $names;
	}

	public function getAvailableParentsPermissionsNames(): array {
		$names = [];
		foreach ($this->availableParentPermissions as $name) {
			$item = $this->auth->getPermission($name);
			if ($item !== null) {
				$names[$item->name] = Yii::t('rbac', $item->name);
			}
		}
		return $names;
	}

	public function getActionsNames(): array {
		$names = [];
		foreach ($this->getActions() as $action) {
			$names[$action] = Yii::t('rbac', $action);
		}
		return $names;
	}

//	public function createForms(): array {
//		$actions = $this->getActions();
//		$forms = [];
//		foreach ($actions as $action) {
//			$forms[] = $this->createForm($action);
//		}
//		return $forms;
//	}
//
//	private function createForm(string $action) {
//		return new ModelRbacForm([
//			'action' => $action,
//		]);
//	}
	public function ensurePermission() {
		$name = $this->getPermissionName();
		if ($this->auth->getPermission($name) === null) {
			$permission = $this->auth->createPermission($name);
			$this->auth->add($permission);
		}
	}

	protected function createPermission(): Permission {
		$permission = $this->auth->createPermission($this->getPermissionName());
		$permission->description = Yii::t('rbac', 'Access to model: {modelName} for Action: {action}', [
			'modelName' => $this->modelName,
			'action' => $this->action,
		]);
		return $permission;
	}
}
