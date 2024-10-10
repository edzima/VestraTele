<?php

namespace common\components\rbac;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\rbac\Assignment;
use yii\rbac\Item;
use yii\rbac\Permission;

class ModelAccessManager extends Component {

	public const ACTION_INDEX = 'index';
	public const ACTION_VIEW = 'view';
	public const ACTION_CREATE = 'create';
	public const ACTION_UPDATE = 'update';
	public const ACTION_DELETE = 'delete';

	public const APP_BASIC = 'basic';
	public const APP_FRONTEND = 'frontend';
	public const APP_BACKEND = 'admin';
	public const APP_ADVANCED = [
		self::APP_BACKEND,
		self::APP_FRONTEND,
	];


	public string $action = self::ACTION_INDEX;

	protected string $app = self::APP_FRONTEND;

	public array $availableApps = self::APP_ADVANCED;

	public string $permissionPrefixName = 'modelAccess';

	public string $nameSeparator = ':';

	public array $availableParentRoles = [];
	public array $availableParentPermissions = [];

	/**
	 * @var string|array|ParentsManagerInterface
	 */
	public $auth = 'authManager';

	protected ?ModelRbacInterface $modelRbac = null;

	public function init(): void {
		parent::init();
		$this->auth = Instance::ensure($this->auth, ParentsManagerInterface::class);
	}

	public function getActions(): array {
		return [
			static::ACTION_INDEX,
			static::ACTION_VIEW,
			static::ACTION_CREATE,
			static::ACTION_UPDATE,
			static::ACTION_DELETE,
		];
	}

	public function checkAccess(string|int $userId): bool {
		return $this->auth->checkAccess($userId, $this->getPermissionName());
	}

	public function ensurePermission(): void {
		$name = $this->getPermissionName();
		if ($this->auth->getPermission($name) === null) {
			$permission = $this->createPermission();
			$this->auth->add($permission);
		}
	}

	public function assign(string|int $userId): Assignment {
		if ($this->modelRbac === null) {
			throw new InvalidConfigException('Model must be set.');
		}
		$permission = $this->getPermission();
		if ($permission === null) {
			throw new InvalidConfigException('Permission not exist.');
		}
		return $this->auth->assign($permission, $userId);
	}

	public function setAction(string $action): self {
		$this->action = $action;
		return $this;
	}

	public function setApp(string $name): self {
		if (!empty($this->availableApps) && !in_array($name, $this->availableApps)) {
			throw new InvalidConfigException("App: $name ' not is allowed");
		}
		$this->app = $name;
		return $this;
	}

	public function setModel(ModelRbacInterface $model): self {
		$this->modelRbac = $model;
		$this->modelName = $model->getRbacBaseName();
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
		$parts = [
			$this->permissionPrefixName,
			$this->app,
			$this->modelRbac->getRbacBaseName(),
			$this->action,
		];
		if (!empty($this->modelRbac->getRbacId())) {
			$parts[] = $this->modelRbac->getRbacId();
		}
		return implode($this->nameSeparator, $parts);
	}

	protected function createPermission(): Permission {
		$permission = $this->auth->createPermission($this->getPermissionName());
		$permission->description = Yii::t('rbac', 'Access to model: {modelName} for Action: {action}', [
			'modelName' => $this->modelRbac->getRbacBaseName(),
			'action' => $this->action,
		]);
		return $permission;
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

	public static function createFromModel(ModelRbacInterface $model, array $config = []): self {
		$self = new static($config);
		$self->setModel($model);
		return $self;
	}

}
