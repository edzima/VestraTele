<?php

namespace common\tests\unit\rbac;

use common\components\rbac\DbManager;
use common\components\rbac\ParentsManagerInterface;
use common\tests\unit\Unit;
use yii\caching\DummyCache;

class ParentsManagerTest extends Unit {

	private ParentsManagerInterface $manager;

	public function testWithoutCache() {
		$this->giveManager(['cache' => null]);
		$admin = $this->manager->createRole('admin');
		$postManager = $this->manager->createRole('postManager');
		$this->manager->add($admin);
		$this->manager->add($postManager);
		$this->manager->addChild($admin, $postManager);
		$this->tester->assertEmpty($this->manager->getParentsRoles('admin'));
	}

	public function testGetParentsRoles() {
		$this->giveManager();
		$admin = $this->manager->createRole('admin');
		$postManager = $this->manager->createRole('postManager');
		$author = $this->manager->createRole('author');
		$postView = $this->manager->createPermission('postView');
		$this->manager->add($admin);
		$this->manager->add($postManager);
		$this->manager->add($author);
		$this->manager->add($postView);
		$this->manager->addChild($admin, $postManager);
		$this->manager->addChild($postManager, $author);
		$this->manager->addChild($postManager, $postView);
		$this->manager->addChild($author, $postView);

		$this->manager->checkAccess(1, 'admin');

		$adminParents = $this->manager->getParentsRoles('admin');
		$this->tester->assertEmpty($adminParents);

		$postManagerParents = $this->manager->getParentsRoles('postManager');
		$this->tester->assertNotEmpty($postManagerParents);
		$this->tester->assertContains('admin', $postManagerParents);
		$this->tester->assertNotContains('author', $postManagerParents);

		$authorParents = $this->manager->getParentsRoles('author');
		$this->tester->assertNotEmpty($authorParents);
		$this->tester->assertContains('postManager', $authorParents);
		$this->tester->assertNotContains('loginToBackend', $authorParents);
		$this->tester->assertNotContains('admin', $authorParents);
	}

	public function testGetParentsPermissions() {
		$this->giveManager();
		$admin = $this->manager->createRole('admin');
		$postManager = $this->manager->createRole('postManager');
		$postDelete = $this->manager->createPermission('postDelete');
		$postCreate = $this->manager->createPermission('postCreate');
		$postDelete = $this->manager->createPermission('postDelete');
		$postView = $this->manager->createPermission('postView');

		$this->manager->add($admin);
		$this->manager->add($postManager);
		$this->manager->add($postDelete);
		$this->manager->add($postCreate);
		$this->manager->add($postView);

		$this->manager->addChild($admin, $postManager);
		$this->manager->addChild($postManager, $postCreate);
		$this->manager->addChild($postManager, $postDelete);
		$this->manager->addChild($postCreate, $postView);

		$this->manager->checkAccess(1, 'admin');

		$this->assertEmpty($this->manager->getParentsPermissions('admin'));
		$this->assertEmpty($this->manager->getParentsPermissions('postManager'));

		$this->assertEmpty($this->manager->getParentsPermissions('postCreate'));

		$postViewParents = $this->manager->getParentsPermissions('postView');
		$this->tester->assertNotEmpty($postViewParents);
		$this->tester->assertContains('postCreate', $postViewParents);
	}

	protected function giveManager(array $config = []): void {
		if (empty($config)) {
			$config['cache'] = DummyCache::class;
		}
		$this->manager = new DbManager($config);
	}

}
