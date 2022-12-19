<?php

namespace common\models\issue;

use common\models\issue\query\IssueQuery;
use common\models\issue\query\IssueStageQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "issue_type".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string $vat
 * @property bool $with_additional_date
 * @property int|null $parent_id
 *
 * @property Issue[] $issues
 * @property IssueStage[] $stages
 * @property static|null $parent
 * @property IssueStageType[] $typeStages
 */
class IssueType extends ActiveRecord {

	private static ?array $TYPES = null;

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%issue_type}}';
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => Yii::t('common', 'Name'),
			'short_name' => Yii::t('common', 'Shortname'),
			'vat' => 'VAT (%)',
			'with_additional_date' => Yii::t('common', 'With additional Date'),
			'parent_id' => Yii::t('issue', 'Type Parent'),
			'parent' => Yii::t('issue', 'Type Parent'),
			'parentName' => Yii::t('issue', 'Type Parent'),

		];
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getIssues(): IssueQuery {
		return $this->hasMany(Issue::class, ['type_id' => 'id']);
	}

	public function getTypeStages(): ActiveQuery {
		return $this->hasMany(IssueStageType::class, ['type_id' => 'id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getStages(): IssueStageQuery {
		return $this->hasMany(IssueStage::class, ['id' => 'stage_id'])
			->orderBy(['posi' => SORT_DESC, 'name' => SORT_ASC])
			->viaTable('{{%issue_stage_type}}', ['type_id' => 'id']);
	}

	public function getParent(): ActiveQuery {
		return $this->hasOne(static::class, ['id' => 'parent_id']);
	}

	public function getNameWithShort(): string {
		return $this->name . ' (' . $this->short_name . ')';
	}

	public static function getTypesIds(): array {
		return array_keys(static::getTypes());
	}

	public static function getShortTypesNames(): array {
		return ArrayHelper::map(static::getTypes(), 'id', 'short_name');
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(static::getTypes(), 'id', 'name');
	}

	public static function getTypesNamesWithShort(): array {
		return ArrayHelper::map(static::getTypes(), 'id', 'nameWithShort');
	}

	public static function get(int $typeId): ?self {
		return static::getTypes()[$typeId] ?? null;
	}

	public function getParentName(): ?string {
		if ($this->parent_id) {
			return static::getTypesNames()[$this->parent_id] ?? null;
		}
		return null;
	}

	/**
	 * @return static[]
	 */
	public static function getTypes(): array {
		if (empty(static::$TYPES)) {
			static::$TYPES = static::find()
				->orderBy('name')
				->indexBy('id')
				->all();
		}
		return static::$TYPES;
	}

	public static function getParents(): array {
		$types = static::getTypes();
		$parents = [];
		foreach ($types as $type) {
			if ($type->parent_id && !isset($parents[$type->parent_id])) {
				$parent = $types[$type->parent_id] ?? null;
				if ($parent) {
					$parents[$parent->id] = $parent;
				}
			}
		}
		return $parents;
	}

}
