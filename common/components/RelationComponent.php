<?php

namespace common\components;

use common\models\hierarchy\ActiveHierarchy;
use common\models\hierarchy\RelationModel;
use common\models\user\User;
use common\models\user\UserRelation;
use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class RelationComponent extends Component {

	public const CHILDS_ARRAY_KEY = 'childs';

	// to
	public $primaryKeyColumn = 'id';
	// from
	public $parentColumn = 'parent_id';
	/** @var string|ActiveRecord */
	public $modelClass = User::class;

	public ?array $parentsData = null;

	/**
	 * @var string|ActiveRecord|RelationModel
	 */
	public $relationModel = UserRelation::class;

	public bool $allowSelf = false;

	private array $tree = [];

	public function unassign(string $type, int $from = null, int $to = null): bool {
		$condition = ['and'];
		$condition[] = [
			$this->relationModel::typeAttribute() => $type,
		];
		if ($to) {
			$condition[] = [$this->relationModel::toAttribute() => $to];
		}
		if ($from) {
			$condition[] = [$this->relationModel::fromAttribute() => $from];
		}
		$this->relationModel::deleteAll($condition);
		return true;
	}

	public function assign(string $type, int $from, int $to): bool {
		if ($this->allowSelf && $from === $to) {
			throw new InvalidArgumentException('$from must be other than $to when disallow self relation.');
		}
		$model = new $this->relationModel([
			$this->relationModel::fromAttribute() => $from,
			$this->relationModel::toAttribute() => $to,
			$this->relationModel::typeAttribute() => $type,
		]);
		return $model->validate() && $model->save();
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
			if ($key === $this->relationModel::toAttribute()) {
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
		$model = $this->relationModel::find()
			->andWhere([
				$this->relationModel::toAttribute() => $id,
				$this->relationModel::typeAttribute() => UserRelation::TYPE_SUPERVISOR,
			])
			->one();
		if ($model instanceof RelationModel) {
			return $model->getFromId();
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
				$this->relationModel::toAttribute(),
				$this->relationModel::fromAttribute())
		);
	}

	private function getParentsData(): array {
		if (empty($this->parentsData)) {
			$this->parentsData = $this->relationModel::find()
				->select([$this->relationModel::toAttribute(), $this->relationModel::fromAttribute()])
				->andWhere(['type' => UserRelation::TYPE_SUPERVISOR])
				->asArray()
				->all();
		}
		if (empty($this->parentsData)) {
			Yii::warning('Empty Supervisor Data', 'relation.getParentsData()');
		}
		return $this->parentsData;
	}

	private function buildTree(array $items): array {
		$childs = [];
		$this->parentColumn = $this->relationModel::fromAttribute();
		$this->primaryKeyColumn = $this->relationModel::toAttribute();
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
