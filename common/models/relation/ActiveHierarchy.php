<?php

namespace common\models\relation;

use yii\db\ActiveQuery;

/**
 * Interface HierarchyModel
 *
 * @property ActiveHierarchy|null $parent
 * @property ActiveHierarchy[] $childes
 */
interface ActiveHierarchy extends Hierarchy {

	public function getParent(): ActiveQuery;

	public function getParents(): ActiveQuery;

}
