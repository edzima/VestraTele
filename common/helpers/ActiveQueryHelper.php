<?php

namespace common\helpers;

use yii\db\ActiveQuery;

class ActiveQueryHelper {

	public static function hasAlreadyJoinedWithRelation(ActiveQuery $query, string $relation): bool {
		foreach ((array) $query->joinWith as $with) {
			$relations = $with[0];
			foreach ($relations as $key => $value) {
				$relationName = is_string($value) ? $value : $key;
				if ($relationName === $relation) {
					return true;
				}
			}
		}
		return false;
	}
}
