<?php

namespace common\components\rbac;

use ReflectionClass;
use Yii;
use yii\base\Component;
use yii\di\Instance;
use yii\rbac\Assignment;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;

class ModelAccess extends Component {

	public const ACTION_INDEX = 'index';
	public const ACTION_VIEW = 'view';
	public const ACTION_CREATE = 'create';
	public const ACTION_UPDATE = 'update';
	public const ACTION_DELETE = 'delete';

	public function getActions(): array {
		return [
			static::ACTION_INDEX,
			static::ACTION_VIEW,
			static::ACTION_CREATE,
			static::ACTION_UPDATE,
			static::ACTION_DELETE,
		];
	}

	public string $managerRolePrefix = 'manager';

	public ?string $modelName = null;

	public string $action = self::ACTION_INDEX;

	/**
	 * @var string|array|ParentsManagerInterface
	 */
	public $auth = 'authManager';

	private string $permissionName;

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
		return $this->modelName . ':' . $this->action;
	}

	protected function getRoleName(): string {
		return $this->managerRolePrefix . ':' . $this->modelName;
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
		return $permission;
	}
}
