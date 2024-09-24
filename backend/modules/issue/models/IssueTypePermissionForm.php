<?php

namespace backend\modules\issue\models;

use common\components\IssueTypeUser;
use common\models\issue\IssueType;
use common\models\user\Worker;
use Yii;
use yii\base\Model;
use yii\di\Instance;

class IssueTypePermissionForm extends Model {

	/**
	 * @var string|array|IssueTypeUser
	 */
	public $issueTypeUser = 'issueTypeUser';

	public ?int $typeId = null;

	public $roles = [];
	public $permissions = [];

	private ?IssueType $model = null;

	public function rules(): array {
		return [
			[['typeId'], 'required'],
			['typeId', 'integer'],
			['typeId', 'in', 'range' => array_keys($this->getTypesNames())],
			[['roles', 'permissions'], 'default', 'value' => []],
			['roles', 'in', 'range' => array_keys($this->getRolesNames()), 'allowArray' => true],
			['permissions', 'in', 'range' => array_keys($this->getPermissionsNames()), 'allowArray' => true],
		];
	}

	public function attributeLabels(): array {
		return [
			'roles' => Yii::t('backend', 'Roles'),
			'permissions' => Yii::t('backend', 'Permissions'),
		];
	}

	public function init(): void {
		parent::init();
		$this->issueTypeUser = Instance::ensure($this->issueTypeUser, IssueTypeUser::class);
	}

	public function setModel(IssueType $model): void {
		$this->model = $model;
		$this->typeId = $model->id;
		$this->roles = $this->issueTypeUser->getParentsRoles($model->id);
		$this->permissions = $this->issueTypeUser->getParentsPermissions($model->id);
	}

	public function getModel(): ?IssueType {
		if ($this->model === null || $this->model->id !== $this->typeId) {
			$this->model = IssueType::findOne($this->typeId);
		}
		return $this->model;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$this->issueTypeUser->ensurePermission($this->typeId);
		$this->issueTypeUser->removeFromParents($this->typeId);
		foreach ($this->permissions as $permissionName) {
			$permission = $this->issueTypeUser->auth->getPermission($permissionName);
			if ($permission) {
				$this->issueTypeUser->addChild($permission, $this->typeId);
			}
		}
		foreach ($this->roles as $rolesName) {
			$role = $this->issueTypeUser->auth->getRole($rolesName);
			if ($role) {
				$this->issueTypeUser->addChild($role, $this->typeId);
			}
		}
		return true;
	}

	public function getTypesNames(): array {
		return IssueType::getTypesNames();
	}

	public function getRolesNames(): array {
		return Worker::getRolesNames();
	}

	public function getPermissionsNames(): array {
		$permissions = [
			Worker::PERMISSION_ISSUE,
		];
		$names = [];
		foreach ($permissions as $permission) {
			$names[$permission] = Worker::getPermissionsNames()[$permission];
		}
		return $names;
	}

}
