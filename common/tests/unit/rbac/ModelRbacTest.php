<?php

namespace common\tests\unit\rbac;

use common\components\rbac\ModelAccess;
use common\tests\unit\Unit;

class ModelRbacTest extends Unit {

	private ModelAccess $modelPermission;

	public function testGetManager() {
		$this->giveModelPermission();
		$this->modelPermission->getManagerRole();
	}

	public function testGetParentsRoles() {
		//$this->modelPermission->getParentsPermissions();
	}

	public function testGetParentsPermissions() {

	}

	private function giveModelPermission(array $config = []) {
		$this->modelPermission = new ModelAccess($config);
	}
}
