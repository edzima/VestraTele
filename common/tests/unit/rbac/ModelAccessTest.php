<?php

namespace common\tests\unit\rbac;

use common\components\rbac\AccessPermission;
use common\components\rbac\ModelAccessManager;
use common\components\rbac\ModelRbacInterface;
use common\tests\unit\Unit;
use yii\base\InvalidConfigException;

class ModelAccessTest extends Unit {

	private ModelRbacInterface $rbacModel;

	private ModelAccessManager $manager;

	protected const DEFAULT_ACTION = 'testAction';
	protected const DEFAULT_APP = 'test-app';

	protected const DEFAULT_APPS_ACTIONS = [
		self::DEFAULT_APP => [
			self::DEFAULT_ACTION,
		],
	];

	public function testSetAppWithoutAppsActions() {
		$this->giveManager();
		$manager = $this->manager;
		$manager->setAppsActions([]);
		$manager->setApp('test-id');
	}

	public function testSetNotAvailableApp() {
		$this->giveManager([
			'appsActions' => [
				'test-2' => [
					'view',
					'create',
				],
				'test-3' => [
					'view',
				],
			],
		]);

		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			$this->manager->setApp('test-1');
		});
	}

	public function testCheckAccessWithoutAssign() {
		$this->giveManager();

		$manager = $this->manager;
		$manager
			->setApp(static::DEFAULT_APP)
			->setAction('testAccess');

		$this->tester->expectThrowable(InvalidConfigException::class, function () {
			$this->manager->checkAccess('test-user');
		});
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

	public function testGetAccessPermissions(): void {
		$this->giveManager();
		$this->setRbacModel();
		$permissions = $this->manager->getAccessPermissions();
		$this->tester->assertCount(0, $permissions);

		$this->manager->setAction('testOneAction');
		$this->manager->ensurePermission();
		$permissions = $this->manager->getAccessPermissions();

		$this->tester->assertCount(1, $permissions);

		$this->manager->setAction('testDoubleAction');
		$this->manager->ensurePermission();

		$permissions = $this->manager->getAccessPermissions();
		$this->tester->assertCount(1, $permissions);

		$permissions = $this->manager->getAccessPermissions(AccessPermission::COMPARE_WITHOUT_APP_AND_ACTION);
		$this->tester->assertCount(2, $permissions);
	}

	public function testDoubleAssign() {
		$this->giveManager();
		$this->setRbacModel(1);
		$this->manager->ensurePermission();

		$this->manager->assign('test-user');
		$this->manager->assign('test-user');
	}

	public function testGetIds(): void {
		$this->giveManager();
		$this->setRbacModel(1);
		$this->manager->setAction('testOneAction');
		$this->manager->ensurePermission();
		$permissions = $this->manager->getAccessPermissions();

		$this->tester->assertCount(1, $permissions);

		$this->manager->setAction('testDoubleAction');
		$this->manager->ensurePermission();
		$this->manager->assign('test-user');

		$this->setRbacModel(2);
		$this->manager->setAction('testOneAction');
		$this->manager->ensurePermission();

		$this->manager->assign('test-user');
		$this->manager->assign('test-user-2');

		$this->manager->setAction('testDoubleAction');
		$this->manager->ensurePermission();

		$this->manager->assign('test-user');
		$this->manager->assign('test-user-2');

		$permissions = $this->manager->getAccessPermissions(AccessPermission::COMPARE_WITHOUT_APP_AND_ACTION);
		$this->tester->assertCount(2, $permissions);

		$ids = $this->manager->getIds();
		$this->tester->assertCount(2, $ids);

		$ids = $this->manager->getIds('test-user');
		$this->tester->assertCount(2, $ids);

		$ids = $this->manager->getIds('test-user-2');
		$this->tester->assertCount(1, $ids);
	}

	private function giveManager(array $config = []) {
		if (!isset($config['appsActions'])) {
			$config['appsActions'] = self::DEFAULT_APPS_ACTIONS;
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
