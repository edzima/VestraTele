<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-05-09
 * Time: 14:58
 */

namespace common\components;

use yii\rbac\DbManager as BaseDbManager;
use yii\rbac\Item;

class DbManager extends BaseDbManager {

	public function removeFromParents(string $name): int {
		$item = $this->getItem($name);
		$count = 0;

		if ($item) {
			$parents = $this->parents[$item->name] ?? [];
			foreach ($parents as $parent) {
				$parent = $this->getItem($parent);
				if ($this->removeChild($parent, $item)) {
					$count++;
				}
			}
		}

		return $count;
	}

	public function getParentsRoles(string $name) {
		$parents = $this->parents[$name] ?? [];
		$items = [];
		foreach ($parents as $parentName) {
			$item = $this->getItem($parentName);
			if ($item->type == Item::TYPE_ROLE) {
				$items[] = $parentName;
			}
		}
		return $items;
	}

	public function getParentsPermissions(string $name) {
		$parents = $this->parents[$name] ?? [];
		$items = [];
		foreach ($parents as $parentName) {
			$item = $this->getItem($parentName);
			if ($item->type == Item::TYPE_PERMISSION) {
				$items[] = $parentName;
			}
		}
		return $items;
	}
}
