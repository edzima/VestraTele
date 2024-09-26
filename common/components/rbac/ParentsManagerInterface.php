<?php

namespace common\components\rbac;

use yii\rbac\ManagerInterface;

interface ParentsManagerInterface extends ManagerInterface {

	public function removeChildFromParents(string $name, array $parents = []): int;

	public function getParentsRoles(string $name, array $parents = []): array;

	public function getParentsPermissions(string $name, array $parents = []): array;

}
