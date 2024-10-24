<?php

namespace common\components\rbac\form;

use common\components\rbac\ModelAccessManager;
use common\models\user\User;
use Yii;
use yii\base\Model;

class SingleActionAccessForm extends Model {

	public $roles = [];
	public $permissions = [];

	public $usersIds = [];

	public string $app;

	public string $action;

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
			['usersIds', 'in', 'range' => array_keys($this->getUsersNames()), 'allowArray' => true],

		];
	}

	public function attributeLabels(): array {
		return [
			'roles' => Yii::t('rbac', 'Roles'),
			'permissions' => Yii::t('rbac', 'Permissions'),
			'usersIds' => Yii::t('rbac', 'Users'),
		];
	}

	protected function setAccess(ModelAccessManager $model): void {
		$this->access = $model;
		$this->action = $model->action;
		$this->app = $model->getApp();
		$this->roles = $model->getParentsRoles();
		$this->permissions = $model->getParentsPermissions();
		$this->usersIds = $model->getUserIds();
	}

	public function setAction(string $action): void {
		$this->action = $action;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$this->access->remove();
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

	public function getUsersNames(): array {
		$names = array_keys($this->getPermissionsNames()) + array_keys($this->getRolesNames());
		return User::getSelectList(
			User::getAssignmentIds($names, false)
		);
	}

	public function getName(): string {
		return Yii::t('rbac', '{app}: {action}', [
			'app' => Yii::t('rbac', $this->app),
			'action' => Yii::t('rbac', $this->action),
		]);
	}
}
