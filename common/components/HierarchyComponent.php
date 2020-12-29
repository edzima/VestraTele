<?php

namespace common\components;

use common\models\user\User;
use yii\base\Component;
use yii\db\ActiveRecord;

class HierarchyComponent extends Component {

	public const CHILDS_ARRAY_KEY = 'childs';

	public $primaryKeyColumn = 'id';
	public $parentColumn = 'parent_id';
	public $modelClass = User::class;

	private array $tree = [];

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
