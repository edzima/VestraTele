<?php

namespace common\models\hierarchy;

use yii\db\ActiveQuery;

/**
 * Interface HierarchyModel
 *
 * @property ActiveHierarchy|null $parent
 * @property ActiveHierarchy[] $childes
 */
interface ActiveHierarchy extends Hierarchy {

	public function getParent(): ActiveQuery;

	public function getParentsQuery(): ActiveQuery;

}
