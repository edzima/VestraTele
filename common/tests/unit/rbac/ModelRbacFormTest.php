<?php

namespace common\tests\unit\rbac;

use common\components\rbac\form\SingleActionAccessForm;
use common\components\rbac\ModelAccessManager;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\caching\DummyCache;

class ModelRbacFormTest extends Unit {

	use UnitModelTrait;

	private SingleActionAccessForm $model;

	private ModelAccessManager $manager;

	public function testEmpty(): void {
		$this->giveManager();
		$this->giveModel();
		$this->thenSuccessValidate();
	}

	public function testSavePermissionWithoutSetAvailable(): void {
		$this->giveManager();
		$this->giveModel();
		$this->model->permissions = ['test-permission'];
		$this->thenUnsuccessSave();
		$this->thenSeeError('Permissions is invalid.', 'permissions');
	}

	public function testSavePermission(): void {
		$this->giveManager();
		$permission = $this->givePermission();
		$this->manager->availableParentPermissions = [
			$permission,
		];
		$this->manager->setModel(new TestRbacModel());
		$this->giveModel();
		$this->model->permissions = [$permission];
		$this->thenSuccessSave();
		$this->manager->auth->hasChild(
			$this->manager->auth->getPermission($permission),
			$this->manager->auth->getPermission(
				$this->manager->getPermissionName()
			),
		);
	}

	private function giveModel(array $config = []): void {
		$this->model = new SingleActionAccessForm($this->manager, $config);
	}

	private function giveManager(array $config = []): void {
		if (!isset($config['app'])) {
			$config['app'] = 'test-app';
		}
		if (!isset($config['action'])) {
			$config['action'] = 'test-action';
		}
		$this->manager = new ModelAccessManager($config);
		$this->manager->auth->cache = new DummyCache();
		$this->manager->setModel(new TestRbacModel());
	}

	public function getModel(): SingleActionAccessForm {
		return $this->model;
	}

	private function givePermission(string $name = 'test-permission'): string {
		$permission = $this->manager->auth->getPermission($name);
		if ($permission === null) {
			$permission = $this->manager->auth->createPermission($name);
			$this->manager->auth->add($permission);
		}
		return $permission->name;
	}
}
