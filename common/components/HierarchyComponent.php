<?php

namespace common\components;

use common\models\hierarchy\ActiveHierarchy;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class HierarchyComponent extends Component {

	public $primaryKeyColumn = 'id';
	public $parentColumn = 'parent_id';
	/** @var string|ActiveRecord|ActiveHierarchy */
	public $modelClass;

	public string $keyChildren = 'children';

	public bool $onlyWithParents = true;

	public array $treeSelect = [];

	private array $tree = [];

	private array $_parentsData = [];

	public function getModel(int $id): ?ActiveHierarchy {
		return $this->modelClass::findOne($id);
	}

	public function unassign(int $id): bool {
		$attributes = [
			$this->parentColumn => null,
		];
		$condition = [
			$this->primaryKeyColumn => $id,
		];
		/** @var ActiveRecord $model */
		$model = $this->modelClass;
		$model::updateAll($attributes, $condition);
		return true;
	}

	public function assign(int $id, int $parent_id = null): bool {
		if ($id === $parent_id) {
			throw new InvalidArgumentException('$id must be other than $parentId.');
		}
		$attributes = [
			$this->parentColumn => $parent_id,
		];
		$condition = [
			$this->primaryKeyColumn => $id,
		];
		/** @var ActiveRecord $model */
		$model = $this->modelClass;
		$model::updateAll($attributes, $condition);
		return true;
	}

	public function detectLoop($parent, $child): bool {
		if ($child === $parent) {
			return true;
		}

		foreach ($this->getChildesIds($child) as $grandchild) {
			if ($this->detectLoop($parent, $grandchild)) {
				return true;
			}
		}

		return false;
	}

	public function getChildesIds(int $id): array {
		$ids = [];
		$parentsIdsMap = $this->getParentsMap();
		foreach ($parentsIdsMap as $baseId => $parent) {
			if ($parent === $id) {
				$ids[] = $baseId;
			}
		}
		return $ids;
	}

	public function getAllChildesIds(int $id): array {
		$tree = $this->getTree($id);
		if (empty($tree)) {
			return [];
		}
		$ids = [];
		array_walk_recursive($tree, function ($item, $key) use (&$ids) {
			if ($key === $this->primaryKeyColumn) {
				$ids[] = (int) $item;
			}
		});
		return $ids;
	}

	public function getTree($id = null): array {
		if (empty($this->tree)) {
			$this->tree = $this->buildTree($this->getParentsData());
		}
		if ($id !== null) {
			return $this->tree[$id] ?? [];
		}
		return $this->tree;
	}

	public function getAllParentsIds(): array {
		return array_keys($this->getTree());
	}

	public function getParentsIds(int $id): array {
		$map = $this->getParentsMap();
		$ids = [];
		while (($id = $map[$id] ?? null) !== null) {
			$ids[] = $id;
		}
		return $ids;
	}

	protected function getParentsMap(): array {
		return array_map('intval',
			ArrayHelper::map(
				$this->getParentsData(),
				$this->primaryKeyColumn,
				$this->parentColumn)
		);
	}

	private function getParentsData(): array {
		if (empty($this->_parentsData)) {
			/** @var ActiveRecord $model */
			$model = $this->modelClass;
			$select = $this->treeSelect;
			$select[] = $this->primaryKeyColumn;
			$select[] = $this->parentColumn;
			$query = $model::find()
				->select($select)
				->indexBy($this->primaryKeyColumn)
				->asArray();
			if ($this->onlyWithParents) {
				$query->andWhere($this->parentColumn . ' IS NOT NULL');
			}
			$this->_parentsData = $query->all();
		}
		return $this->_parentsData;
	}

	public function nonIndexedTree(array $elements, $parentId = null): array {
		$branch = [];
		foreach ($elements as $element) {
			if ($element[$this->parentColumn] == $parentId) {
				$children = $this->nonIndexedTree($elements, $element[$this->primaryKeyColumn]);
				if ($children) {
					$element[$this->keyChildren] = $children;
				}
				$branch[] = $element;
			}
		}

		return $branch;
	}

	private function buildTree(array $items): array {
		$childs = [];
		foreach ($items as &$item) {
			$childs[$item[$this->parentColumn]][] = &$item;
		}
		unset($item);
		foreach ($items as &$item) {
			if (isset($childs[$item[$this->primaryKeyColumn]])) {
				$item[$this->keyChildren] = $childs[$item[$this->primaryKeyColumn]];
			}
		}
		return $childs;
	}

}
