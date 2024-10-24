<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-05-09
 * Time: 14:58
 */

namespace common\components\rbac;

use Yii;
use yii\rbac\DbManager as BaseDbManager;
use yii\rbac\Item;

class DbManager extends BaseDbManager implements ParentsManagerInterface {

	public function removeChildFromParents(string $name, array $parents = []): int {
		$item = $this->getItem($name);
		$count = 0;

		if ($item) {
			if (empty($parents)) {
				$parents = $this->getParents($name);
			}
			foreach ($parents as $parent) {
				$parent = $this->getItem($parent);
				if ($this->removeChild($parent, $item)) {
					$count++;
				}
			}
		}

		return $count;
	}

	public function getParentsRoles(string $name, array $parents = []): array {
		if (empty($parents)) {
			$parents = $this->getParents($name);
		}

		$items = [];
		foreach ($parents as $parentName) {
			$item = $this->getItem($parentName);
			if ($item->type == Item::TYPE_ROLE) {
				$items[] = $parentName;
			}
		}

		return $items;
	}

	public function getParentsPermissions(string $name, array $parents = []): array {
		if (empty($parents)) {
			$parents = $this->getParents($name);
		}

		$items = [];
		foreach ($parents as $parentName) {
			$item = $this->getItem($parentName);
			if ($item->type == Item::TYPE_PERMISSION) {
				$items[] = $parentName;
			}
		}
		return $items;
	}

	protected function getParents(string $name): array {
		if (empty($this->parents)) {
			if ($this->items === null && $this->cache === null) {
				Yii::warning('getParents() only with cache enabled.', __METHOD__);
			}
			$this->loadFromCache();
		}
		return $this->parents[$name] ?? [];
	}
}
