<?php

namespace common\tests\unit\models;

use common\fixtures\user\AgentFixture;
use common\models\user\Worker;
use common\tests\unit\Unit;

class HierarchyTest extends Unit {

	public function _fixtures(): array {
		return [
			'worker' => [
				'class' => AgentFixture::class,
				'dataFile' => codecept_data_dir() . 'user/agent.php',
			],
		];
	}

	public function testParentsWithoutHasParents(): void {
		$withoutParents = $this->grabWorker('with-childs');
		$this->tester->assertCount(0, $withoutParents->getParentsIds());
		$withoutParents = $this->grabWorker('without-parent-and-childs');
		$this->tester->assertCount(0, $withoutParents->getParentsIds());
	}

	public function testParentsAsFirstChild(): void {
		$worker = $this->grabWorker('with-parent-and-child');
		$parentsIds = $worker->getParentsIds();
		$this->tester->assertCount(1, $parentsIds);
		$this->tester->assertTrue(in_array(300, $parentsIds));
		$parentsIds = \Yii::$app->userHierarchy->getParentsIds($worker->id);
		$this->tester->assertCount(1, $parentsIds);
		$this->tester->assertTrue(in_array(300, $parentsIds));
	}

	public function testParentsAsChildFromChild(): void {
		$worker = $this->grabWorker('with-parents-without-childs');
		$parentsIds = $worker->getParentsIds();
		$this->tester->assertCount(2, $parentsIds);
		$this->tester->assertTrue(in_array(301, $parentsIds));
		$this->tester->assertTrue(in_array(300, $parentsIds));
	}

	public function testChildrenWhenHasDobuleHierarchyChildren(): void {
		$worker = $this->grabWorker('with-childs');
		$childesIds = $worker->getChildesIds();
		$this->tester->assertCount(1, $childesIds);
		$this->tester->assertTrue(in_array(301, $childesIds));
		$allChildesIds = $worker->getAllChildesIds();
		$this->tester->assertCount(2, $allChildesIds);
		$this->tester->assertTrue(in_array(301, $allChildesIds));
		$this->tester->assertTrue(in_array(302, $allChildesIds));
	}

	private function grabWorker($index): Worker {
		return $this->tester->grabFixture('worker', $index);
	}
}
