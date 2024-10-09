<?php

namespace common\tests\unit\rbac;

use common\components\rbac\ModelAccessManager;
use common\components\rbac\ModelRbacInterface;
use common\tests\unit\Unit;
use yii\base\InvalidConfigException;

class ModelAccessTest extends Unit {

	private ModelRbacInterface $rbac;

	private ModelAccessManager $manager;

	public function testSetAppWithoutAvailableApps() {
		$manager = new ModelAccessManager();
		$manager->availableApps = [];
		$manager->setApp('test-id');
	}

	public function testSetNotAvailableApp() {
		$manager = new ModelAccessManager();
		$manager->availableApps = [
			'test-2',
			'test-3',
		];
		$this->tester->expectThrowable(InvalidConfigException::class, function () use ($manager) {
			$manager->setApp('test-1');
		});
	}

	public function testCheckAccessWithoutAssign() {
		$manager = new ModelAccessManager();
		$manager->availableApps = [
			'test-1',
			'test-2',
		];

		$manager
			->setApp('test-1')
			->setAction('testAccess');

		$this->tester->assertFalse($manager->hasAccess('test-user'));
	}

	public function testAssignWithoutSetRbacModel() {
		$manager = new ModelAccessManager();
		$this->tester->expectThrowable(new InvalidConfigException('Rbac model must be set.'), function () use ($manager) {
			$manager->assign('test-user');
		});
	}

	public function testAssignWithRbacModelWithoutPermission() {
		$manager = new ModelAccessManager();
		$rbac = new TestRbacModel();
		$manager->setModel($rbac);
		$this->tester->expectThrowable(new InvalidConfigException('Permission not already exist.'), function () use ($manager) {
			$manager->assign('test-user');
		});
	}

	private function giveManager(array $config = []) {
		$this->manager = new ModelAccessManager($config);
	}

	protected function setRbacModel(string $name = 'test-rbac'): void {
		$this->rbacModel = new TestRbacModel($name);
		$this->manager->setModel($this->rbacModel);
	}

}
