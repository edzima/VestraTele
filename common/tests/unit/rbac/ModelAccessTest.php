<?php

namespace common\tests\unit\rbac;

use common\components\rbac\ModelAccessManager;
use common\components\rbac\ModelRbacInterface;
use common\tests\unit\Unit;
use yii\base\InvalidConfigException;

class ModelAccessTest extends Unit {

	private ModelRbacInterface $rbacModel;

	private ModelAccessManager $manager;

	protected const DEFAULT_ACTION = 'testAction';
	protected const DEFAULT_APP = 'test-app';

	protected const DEFAULT_AVAILABLE_APPS = [
		'test-app',
	];

	public function testSetAppWithoutAvailableApps() {
		$this->giveManager();
		$manager = $this->manager;
		$manager->availableApps = [];
		$manager->setApp('test-id');
	}

	public function testSetNotAvailableApp() {
		$this->giveManager([
			'availableApps' => [
				'test-2',
				'test-3',
			],
		]);

		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			$this->manager->setApp('test-1');
		});
	}

	public function testCheckAccessWithoutAssign() {
		$this->giveManager([
			'availableApps' => [
				'test-1',
				'test-2',
			],
		]);

		$manager = $this->manager;
		$manager
			->setApp('test-1')
			->setAction('testAccess');

		$this->tester->assertFalse($manager->checkAccess('test-user'));
	}

	public function testAssignWithoutSetRbacModel() {
		$this->giveManager();
		$this->tester->expectThrowable(new InvalidConfigException('Model must be set.'), function () {
			$this->manager->assign('test-user');
		});
	}

	public function testAssignWithRbacModelWithoutCreatedPermission() {
		$this->giveManager();
		$this->setRbacModel();
		$this->tester->expectThrowable(new InvalidConfigException('Permission not exist.'), function () {
			$this->manager->assign('test-user');
		});
	}

	public function testCheckAccess(): void {
		$this->giveManager();
		$this->setRbacModel();
		$this->tester->assertFalse($this->manager->checkAccess('test-user'));
		$this->manager->ensurePermission();
		$this->tester->assertFalse($this->manager->checkAccess('test-user'));
		$this->manager->assign('test-user');
		$this->tester->assertTrue($this->manager->checkAccess('test-user'));
		$this->tester->assertFalse($this->manager->checkAccess('test-user2'));
	}

	public function testMultipleAction() {
		$this->giveManager();
		$this->setRbacModel();
		$this->manager->setAction('testOneAction');
		$this->manager->ensurePermission();
		$this->manager->assign('test-user');

		$this->tester->assertTrue($this->manager
			->checkAccess('test-user')
		);

		$this->tester->assertFalse($this->manager
			->setAction('testDoubleAction')
			->checkAccess('test-user')
		);

		$this->manager->ensurePermission();
		$this->tester->assertFalse($this->manager
			->checkAccess('test-user')
		);

		$this->manager->assign('test-user');
		$this->tester->assertTrue($this->manager
			->checkAccess('test-user')
		);
	}

	public function testGetPermissions(): void {
		$this->giveManager();
		$this->setRbacModel();
		$permissions = $this->manager->getPermissions();
		$this->tester->assertCount(0, $permissions);

		$this->manager->setAction('testOneAction');
		$this->manager->ensurePermission();

		$this->manager->setAction('testDoubleAction');
		$this->manager->ensurePermission();

		$permissions = $this->manager->getPermissions();
		$this->tester->assertCount(2, $permissions);
	}

	private function giveManager(array $config = []) {
		if (!isset($config['availableApps'])) {
			$config['availableApps'] = self::DEFAULT_AVAILABLE_APPS;
			if (!isset($config['app'])) {
				$config['app'] = self::DEFAULT_APP;
			}
		}

		if (!isset($config['action'])) {
			$config['action'] = self::DEFAULT_ACTION;
		}

		$this->manager = new ModelAccessManager($config);
	}

	protected function setRbacModel(string $id = null, string $name = 'test-rbac'): void {
		$this->rbacModel = new TestRbacModel($name, $id);
		$this->manager->setModel($this->rbacModel);
	}

}
