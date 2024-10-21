<?php

namespace common\components\rbac\form;

use common\components\rbac\ModelAccessManager;
use Yii;
use yii\base\Model;

class ModelRbacForm extends Model {

	public $roles = [];
	public $permissions = [];

	public $usersIds = [];

	public string $action;

	public string $app;
	private ModelAccessManager $access;

	public function __construct(ModelAccessManager $access, array $config = []) {
		parent::__construct($config);
		$this->setAccess($access);
	}

	public function rules(): array {
		return [
			[['!action', '!app'], 'required'],
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

	protected function setAccess(ModelAccessManager $model): void {
		$this->access = $model;
		$this->action = $model->action;
		$this->app = $model->getApp();
		$this->roles = $model->getParentsRoles();
		$this->permissions = $model->getParentsPermissions();
	}

	public function setAction(string $action): void {
		$this->action = $action;
	}

	protected function shouldRemove(): bool {
		return empty($this->permissions)
			&& empty($this->roles)
			&& empty($this->usersIds)
			&& empty($this->access->getUserIds());
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		if ($this->shouldRemove()) {
			$this->access->remove();
			return true;
		}
		if (!empty($this->roles) || !empty($this->permissions) || !empty($this->usersIds)) {
			$this->access->ensurePermission();
		}
		if (!empty($this->usersIds)) {
			foreach ($this->usersIds as $userId) {
				if (!empty($userId)) {
					$this->access->assign($userId);
				}
			}
		}
		$this->access->removeFromAllParents();
		if (!empty($this->roles)) {
			$this->access->addToRoles($this->roles);
		}
		if (!empty($this->permissions)) {
			$this->access->addToPermissions($this->permissions);
		}
		return true;
	}

	public function getRolesNames(): array {
		return $this->access->getAvailableParentRolesNames();
	}

	public function getPermissionsNames(): array {
		return $this->access->getAvailableParentsPermissionsNames();
	}

	public function getName(): string {
		return Yii::t('rbac', '{app}: {action}', [
			'app' => Yii::t('rbac', $this->app),
			'action' => Yii::t('rbac', $this->action),
		]);
	}
}
