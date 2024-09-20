<?php

namespace common\models\hierarchy;

use common\components\HierarchyComponent;
use Yii;
use yii\db\ActiveQuery;

trait HierarchyActiveModelTrait {

	private static ?HierarchyComponent $hierarchy = null;

	protected static function getHierarchyConfig(): array {
		return [
			'class' => HierarchyComponent::class,
			'modelClass' => static::class,
		];
	}

	/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
	public static function getHierarchy(): HierarchyComponent {
		if (static::$hierarchy === null) {
			$config = static::getHierarchyConfig();
			if (!isset($config['class'])) {
				$config['class'] = HierarchyComponent::class;
			}
			if (!isset($config['modelClass'])) {
				$config['modelClass'] = static::class;
			}
			static::$hierarchy = Yii::createObject($config);
		}
		return static::$hierarchy;
	}

	public function detectLoop($parentId): bool {
		return static::getHierarchy()->detectLoop($parentId, $this->{$this->hierarchyKeyAttribute()});
	}

	public function getParentsIds(): array {
		if ($this->getParentId() === null) {
			return [];
		}
		return static::getHierarchy()->getParentsIds($this->{$this->hierarchyKeyAttribute()});
	}

	public function getParentId(): ?int {
		return $this->{$this->hierarchyParentAttribute()};
	}

	public function getChildesIds(): array {
		return static::getHierarchy()->getChildesIds($this->{$this->hierarchyKeyAttribute()});
	}

	public function getAllChildesIds(): array {
		return static::getHierarchy()->getAllChildesIds($this->{$this->hierarchyKeyAttribute()});
	}

	public function getParentsQuery(): ActiveQuery {
		return static::find()->where([
			$this->hierarchyKeyAttribute() => $this->getParentsIds(),
		]);
	}

	protected function hierarchyKeyAttribute(): string {
		return 'id';
	}

	protected function hierarchyParentAttribute(): string {
		return 'parent_id';
	}

}
