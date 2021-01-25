<?php

namespace common\components;

use common\models\hierarchy\HierarchyModel;
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

	private array $tree = [];

	public function getModel(int $id): ?HierarchyModel {
		return $this->modelClass::findOne($id);
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
		array_walk_recursive($selfTree, static function ($item, $key) use (&$ids) {
			if ($key === 'id') {
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

	public function getParent(int $id): ?int {
		$model = $this->getModel($id);
		if ($model) {
			return $model->getParentId();
		}
		return null;
	}

	public function getParentsIds(int $id): array {
		if ($this->getParent($id) === null) {
			return [];
		}
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
		/** @var ActiveRecord $model */
		$model = $this->modelClass;
		return $model::find()
			->select([$this->primaryKeyColumn, $this->parentColumn])
			->andWhere($this->parentColumn . ' IS NOT NULL')
			->asArray()
			->all();
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
