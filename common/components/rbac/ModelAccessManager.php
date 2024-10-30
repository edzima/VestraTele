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

	public const APP_FRONTEND = 'app-frontend';
	public const APP_BACKEND = 'app-backend';

	public const ACTION_INDEX = 'index';
	public const ACTION_VIEW = 'view';
	public const ACTION_CREATE = 'create';
	public const ACTION_UPDATE = 'update';
	public const ACTION_DELETE = 'delete';

	protected array $appsActions = [];

	public string $action = self::ACTION_INDEX;

	protected string $app = self::APP_FRONTEND;

	public array $availableParentRoles = [];
	public array $availableParentPermissions = [];

	public ?string $managerPermission = null;

	/**
	 * @var string|array|ParentsManagerInterface
	 */
	public $auth = 'authManager';

	public $accessPermission = [
		'class' => AccessPermission::class,
		'prefix' => 'modelAccess',
	];

	protected ?ModelRbacInterface $modelRbac = null;

	public function init(): void {
		parent::init();
		$this->auth = Instance::ensure($this->auth, ParentsManagerInterface::class);
	}

	public function setAppsActions(array $appsActions): void {
		$this->appsActions = $appsActions;
	}

	public function getAppsActions(): array {
		return $this->appsActions;
	}

	public function checkAccess(string|int $userId): bool {
		if ($this->managerPermission) {
			$hasAccess = $this->auth->checkAccess($userId, $this->managerPermission);
			if ($hasAccess) {
				return true;
			}
		}
		return $this->auth->checkAccess($userId, $this->getPermissionName());
	}

	public function ensurePermission(string $description = null): void {
		$name = $this->getPermissionName();
		if ($this->auth->getPermission($name) === null) {
			$permission = $this->createPermission($description);
			$this->auth->add($permission);
		}
	}

	public function removeAll(string $type): void {
		$permissions = $this->getPermissions($type);
		foreach ($permissions as $permission) {
			$this->auth->remove($permission);
		}
	}

	public function assign(string|int $userId): ?Assignment {
		if (!$this->hasModel()) {
			throw new InvalidConfigException('Model must be set.');
		}
		$permission = $this->getPermission();
		if ($permission === null) {
			throw new InvalidConfigException('Permission not exist.');
		}
		if ($this->auth->checkAccess($userId, $permission->name)) {
			return null;
		}

		return $this->auth->assign($permission, $userId);
	}

	public function getUserIds(string $name = null): array {
		$name = $name ?? $this->getPermissionName();
		return $this->auth->getUserIdsByRole($name);
	}

	public function hasModel(): bool {
		return $this->modelRbac !== null;
	}

	public function setAction(string $action): self {
		$this->action = $action;
		return $this;
	}

	public function setApp(string $name): self {
		if (!empty($this->appsActions) && !key_exists($name, $this->appsActions)) {
			throw new InvalidConfigException("App: $name ' not is allowed");
		}
		$this->app = $name;
		return $this;
	}

	public function getApp(): string {
		return $this->app;
	}

	public function setModel(ModelRbacInterface $model): self {
		$this->modelRbac = $model;
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

	protected function getPermission(): ?Permission {
		return $this->auth->getPermission($this->getPermissionName());
	}

	public function getParentsRoles(string $name = null): array {
		$name = $name ?? $this->getPermissionName();
		return $this->auth->getParentsRoles($name);
	}

	public function getParentsPermissions(string $name = null): array {
		$name = $name ?? $this->getPermissionName();
		return $this->auth->getParentsPermissions($name);
	}

	public function getIds(string|int $userId = null): array {
		$models = $this->getAccessPermissions(AccessPermission::COMPARE_WITHOUT_ID);
		$ids = [];
		foreach ($models as $model) {
			if ($model->modelId) {
				$ids[$model->name] = $model->modelId;
			}
		}
		if (empty($userId)) {
			return $ids;
		}
		return array_filter($ids, function (string $name) use ($userId) {
			return $this->auth->checkAccess($userId, $name);
		}, ARRAY_FILTER_USE_KEY);
	}

	/**
	 * @param string $type
	 * @return AccessPermission[]
	 */
	public function getAccessPermissions(string $type = AccessPermission::COMPARE_ALL_ATTRIBUTES): array {
		$self = $this->createAccessPermission();
		$data = [];
		foreach ($this->auth->getPermissions() as $permission) {
			$compare = $this->createAccessPermission($permission->name);
			if ($compare->explode()
				&& AccessPermission::compare($self, $compare, $type)) {
				$data[$permission->name] = $compare;
			}
		}
		return $data;
	}

	protected function createPermission(string $description = null): Permission {
		$permission = $this->auth->createPermission($this->getPermissionName());
		$permission->description = !empty($description) ? $description : $this->getPermissionDescription();
		return $permission;
	}

	public function getPermissionName(): string {
		if (!$this->hasModel()) {
			throw new InvalidConfigException('Model must be set.');
		}
		$generator = $this->createAccessPermission();
		$generator->generate();
		return $generator->getName();
	}

	protected function createAccessPermission(string $name = null): AccessPermission {
		$config = $this->accessPermission;
		if (!isset($config['class'])) {
			$config['class'] = AccessPermission::class;
		}
		if ($name === null) {
			$config['app'] = $this->app;
			$config['action'] = $this->action;
			$config['modelName'] = $this->modelRbac->getRbacBaseName();
			$config['modelId'] = $this->modelRbac->getRbacId();
		} else {
			$config['name'] = $name;
		}
		return Yii::createObject($config);
	}

	public function getPermissionDescription(): string {
		if ($this->getPermission()) {
			return $this->getPermission()->description;
		}
		if ($this->modelRbac->getRbacId()) {
			return Yii::t('rbac', 'Access to model: {modelName} with ID: {id} for Action: {action} [{app}]', [
				'modelName' => Yii::t('rbac', $this->modelRbac->getRbacBaseName()),
				'action' => Yii::t('rbac', $this->action),
				'app' => Yii::t('rbac', $this->app),
				'id' => $this->modelRbac->getRbacId(),
			]);
		}
		return Yii::t('rbac', 'Access to model: {modelName} for Action: {action} [{app}]', [
			'modelName' => $this->modelRbac->getRbacBaseName(),
			'action' => $this->action,
			'app' => $this->app,
		]);
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

	public static function createFromModel(ModelRbacInterface $model, array $config = []): self {
		$self = new static($config);
		$self->setApp(Yii::$app->id);
		$self->setModel($model);
		return $self;
	}

	public function remove(): bool {
		$permission = $this->getPermission();
		if ($permission) {
			return $this->auth->remove($permission);
		}
		return false;
	}

}
