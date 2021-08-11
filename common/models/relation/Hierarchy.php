<?php

namespace common\models\relation;

/**
 * Interface HierarchyModel
 *
 * @property Hierarchy|null $parent
 * @property Hierarchy[] $childes
 */
interface Hierarchy {

	public function getParentId(): ?int;

	public function getParentsIds(): array;

	public function getChildesIds(): array;

	public function getAllChildesIds(): array;

}
