<?php

namespace common\models\issue;

use common\models\issue\query\IssueQuery;
use common\models\issue\query\IssueStageQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "issue_stage".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int $posi
 * @property int|null $days_reminder
 * @property string|null $calendar_background
 *
 * @property Issue[] $issues
 * @property IssueStageType[] $stageTypes
 * @property issueType[] $types
 */
class IssueStage extends ActiveRecord {

	public const ARCHIVES_ID = -1;

	public static array $STAGES = [];

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%issue_stage}}';
	}

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => Yii::t('common', 'Name'),
			'short_name' => Yii::t('common', 'Shortname'),
			'posi' => Yii::t('common', 'Order'),
			'days_reminder' => Yii::t('common', 'Reminder (days)'),
			'calendar_background' => Yii::t('common', 'Calendar Background'),
		];
	}

	public function getNameWithShort(): string {
		return $this->name . ' (' . $this->short_name . ')';
	}

	public function getTypesName(): string {
		$names = [];
		foreach ($this->types as $type) {
			$names[] = $type->name;
		}
		return implode(', ', $names);
	}

	public function getTypesShortNames(): string {
		$names = [];
		foreach ($this->types as $type) {
			$names[] = $type->short_name;
		}
		return implode(', ', $names);
	}

	public function getIssues(): IssueQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(Issue::class, ['stage_id' => 'id']);
	}

	public function getTypes(): ActiveQuery {
		return $this->hasMany(IssueType::class, ['id' => 'type_id'])
			->viaTable('{{%issue_stage_type}}', ['stage_id' => 'id'])
			->indexBy('id');
	}

	public function getStageTypes(): ActiveQuery {
		return $this->hasMany(IssueStageType::class, ['stage_id' => 'id']);
	}

	public static function getStagesNames(bool $withArchive = false): array {
		$names = ArrayHelper::map(static::getStages(), 'id', 'nameWithShort');
		if (!$withArchive) {
			unset($names[static::ARCHIVES_ID]);
		}
		return $names;
	}

	/**
	 * @return static[]
	 */
	public static function getStages(): array {
		if (empty(static::$STAGES)) {
			static::$STAGES = static::find()
				->orderBy('name')
				->indexBy('id')
				->all();
		}
		return static::$STAGES;
	}

	public function hasType(int $id): bool {
		$stageTypes = $this->stageTypes;
		foreach ($stageTypes as $stageType) {
			if ($stageType->type_id === $id) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @inheritdoc
	 * @return IssueStageQuery the active query used by this AR class.
	 */
	public static function find(): IssueStageQuery {
		return new IssueStageQuery(static::class);
	}

	public static function get(int $id): ?self {
		return static::getStages()[$id] ?? null;
	}

}
