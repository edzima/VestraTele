<?php

namespace common\components\rbac\form;

use common\components\rbac\ModelAccess;
use Yii;
use yii\base\Model;

class ModelRbacForm extends Model {

	public $roles = [];
	public $permissions = [];
	public $managerUserIds = [];

	public string $action;

	public ?string $app = null;
	private ModelAccess $access;

	public function setAccess(ModelAccess $model): void {
		$this->access = $model;
//		$this->roles = $model->getParentsRoles();
//		$this->permissions = $model->getParentsPermissions();
	}

	public function setAction(string $action): void {
		$this->action = $action;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		if (!empty($this->managerUserIds) || !empty($this->permissions) || !empty($this->roles)) {
			$this->access->ensurePermission();
		}
		if (!empty($this->managerUserIds)) {
			foreach ($this->managerUserIds as $managerUserId) {
				if (!empty($managerUserId)) {
					$this->access->assign($managerUserId, true);
				}
			}
		}
		if (!empty($this->roles)) {
			$this->access->addToRoles($this->roles);
		}
		if (!empty($this->roles)) {
			$this->access->addToPermissions($this->roles);
		}
		return true;
	}

	public function rules(): array {
		return [
			[['roles', 'permissions'], 'default', 'value' => []],
			['roles', 'in', 'range' => array_keys($this->getRolesNames()), 'allowArray' => true],
			['permissions', 'in', 'range' => array_keys($this->getPermissionsNames()), 'allowArray' => true],
		];
	}

	public function attributeLabels(): array {
		return [
			'roles' => Yii::t('rbac', 'Roles'),
			'permissions' => Yii::t('rbac', 'Permissions'),
		];
	}

	public function getRolesNames(): array {
		return $this->access->getAvailableParentRolesNames();
	}

	public function getPermissionsNames(): array {
		return $this->access->getAvailableParentsPermissionsNames();
	}

	public function getActionName(): string {
		return $this->access->getActionsNames()[$this->action];
	}
}
