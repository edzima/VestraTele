<?php

namespace common\components;

use common\models\hierarchy\ActiveHierarchy;
use common\models\user\User;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class HierarchyComponent extends Component {

	public const CHILDS_ARRAY_KEY = 'childs';

	public $primaryKeyColumn = 'id';
	public $parentColumn = 'parent_id';
	/** @var string|ActiveRecord */
	public $modelClass = User::class;

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
		$selfTree = $this->getSelfTree($id);
		if (empty($selfTree)) {
			return [];
		}
		$ids = [];
		array_walk_recursive($selfTree, function ($item, $key) use (&$ids) {
			if ($key === $this->primaryKeyColumn) {
				$ids[] = $item;
			}
		});
		return $ids;
	}

	public function getSelfTree(int $id): array {
		return $this->getTree()[$id] ?? [];
	}

	public function getTree(): array {
		if (empty($this->tree)) {
			$this->tree = $this->buildTree($this->getParentsData());
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
			$this->_parentsData = $model::find()
				->select($select)
				->andWhere($this->parentColumn . ' IS NOT NULL')
				->asArray()
				->all();
		}
		return $this->_parentsData;
	}

	private function buildTree(array $items): array {
		$childs = [];
		foreach ($items as &$item) {
			$childs[$item[$this->parentColumn]][] = &$item;
		}
		unset($item);
		foreach ($items as &$item) {
			if (isset($childs[$item[$this->primaryKeyColumn]])) {
				$item[static::CHILDS_ARRAY_KEY] = $childs[$item[$this->primaryKeyColumn]];
			}
		}
		return $childs;
	}

}
