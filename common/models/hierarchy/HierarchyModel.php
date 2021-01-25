<?php

namespace common\models\hierarchy;

use yii\db\ActiveQuery;

/**
 * Interface HierarchyModel
 *
 * @property HierarchyModel|null $parent
 * @property HierarchyModel[] $childes
 */
interface HierarchyModel {

	public function getParentId(): ?int;

	public function getParentsIds(): array;

	public function getChildesIds(): array;

	public function getAllChildesIds(): array;

	public function getParent(): ActiveQuery;

	public function getParentsQuery(): ActiveQuery;

}
