<?php

namespace common\components;

use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use Yii;
use yii\base\Component;
use yii\di\Instance;
use yii\rbac\Item;
use yii\rbac\ManagerInterface;

class IssueTypeUser extends Component {

	/**
	 * @var string|array|ManagerInterface
	 */
	public $auth = 'authManager';

	private array $usersTypes = [];

	public function init(): void {
		parent::init();
		$this->auth = Instance::ensure($this->auth, ManagerInterface::class);
	}

	public function addPermission(int $typeId): bool {
		$permission = $this->getPermission($typeId);
		if ($permission === null) {
			$permission = $this->createPermission($typeId);
			$this->auth->add($permission);
			return true;
		}
		return false;
	}

	public function removeFromParents(int $typeId): int {
		return $this->auth->removeFromParents($this->getPermissionName($typeId));
	}

	public function addChild(Item $parent, int $typeId): bool {
		$permission = $this->getPermission($typeId);
		if (!$permission ||
			$this->auth->hasChild($parent, $permission)) {
			return false;
		}
		return $this->auth->addChild($parent, $permission);
	}

	public function getUsersIds(int $typeId): array {
		return $this->auth->getUserIdsByRole($this->getPermissionName($typeId));
	}

	public function userHasAccess(int $userId, int $typeId): bool {
		$permission = $this->getPermissionName($typeId);
		if ($permission === null) {
			return false;
		}
		if ($this->auth->checkAccess($userId, $this->getPermissionName($typeId))) {
			return true;
		}
		foreach ($this->getParentsRoles($typeId) as $name) {
			if ($this->auth->checkAccess($userId, $name)) {
				return true;
			}
		}
		foreach ($this->getParentsPermissions($typeId) as $name) {
			if ($this->auth->checkAccess($userId, $name)) {
				return true;
			}
		}
		return false;
	}

	protected function createPermission(int $typeId): Item {
		$permission = $this->auth->createPermission($this->getPermission($typeId));
		$permission->description = Yii::t('issue', 'Issues Type: {name}', [
			'name' => $this->findModel($typeId)->name,
		]);
		return $permission;
	}

	public function getParentsRoles(int $typeId): array {
		return $this->auth->getParentsRoles($this->getPermissionName($typeId));
	}

	public function getParentsPermissions(int $typeId): array {
		return $this->auth->getParentsPermissions($this->getPermissionName($typeId));
	}

	protected function getPermission(int $typeId): ?Item {
		return $this->auth->getPermission($this->getPermissionName($typeId));
	}

	protected function getPermissionName(int $typeId): string {
		return 'issue.types:' . $typeId;
	}

	public function userHasIssues(int $userId, int $typeId, bool $withChildren = true): bool {
		$typesIds = $this->getLinkedUserTypes($userId);
		if (in_array($typeId, $typesIds)) {
			return true;
		}
		if ($withChildren) {
			$model = $this->findModel($typeId);
			if ($model) {
				foreach ($model->getAllChildesIds() as $childId) {
					if (in_array($childId, $typesIds)) {
						return true;
					}
				}
			}
		}

		return false;
	}

	protected function getLinkedUserTypes(int $userId, bool $refresh = false): array {
		if (!isset($this->usersTypes[$userId])) {
			$this->usersTypes[$userId] = IssueUser::find()
				->joinWith('issue I')
				->users([$userId])
				->select('I.type_id')
				->distinct()
				->column();
		}
		return $this->usersTypes[$userId];
	}

	protected function findModel(int $typeId): ?IssueType {
		return IssueType::get($typeId);
	}

	public function getPermissions(int $typeId): array {
		return $this->auth->getChildren($this->getPermissionName($typeId));
	}

}
