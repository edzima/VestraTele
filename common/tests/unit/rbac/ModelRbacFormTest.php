<?php

namespace common\tests\unit\rbac;

use common\components\rbac\form\ModelRbacForm;
use common\components\rbac\ModelAccessManager;
use common\models\user\Worker;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\caching\DummyCache;

class ModelRbacFormTest extends Unit {

	use UnitModelTrait;

	private ModelRbacForm $model;

	private ModelAccessManager $manager;

	public function testEmpty(): void {
		$this->giveManager();
		$this->giveModel();
		$this->thenSuccessValidate();
	}

	public function testSavePermissionWithoutSetAvailable(): void {
		$this->giveManager();
		$this->giveModel();
		$this->model->permissions = [Worker::PERMISSION_ISSUE];
		$this->thenUnsuccessSave();
		$this->thenSeeError('Permissions is invalid.', 'permissions');
	}

	public function testSavePermission(): void {
		$this->giveManager();
		$this->manager->availableParentPermissions = [
			Worker::PERMISSION_ISSUE,
		];
		$this->manager->setModel(new TestRbacModel());
		$this->giveModel();
		$this->model->permissions = [Worker::PERMISSION_ISSUE];
		$this->thenSuccessSave();
		$this->manager->auth->hasChild(
			$this->manager->auth->getPermission(Worker::PERMISSION_ISSUE),
			$this->manager->auth->getPermission(
				$this->manager->getPermissionName()
			),
		);
	}

	private function giveModel(array $config = []): void {
		$this->model = new ModelRbacForm($this->manager, $config);
	}

	private function giveManager(array $config = []): void {
		$this->manager = new ModelAccessManager($config);
		$this->manager->auth->cache = new DummyCache();
		$this->manager->setModel(new TestRbacModel());
	}

	public function getModel(): ModelRbacForm {
		return $this->model;
	}
}
