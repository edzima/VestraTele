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
 * @property int $days_reminder
 *
 * @property Issue[] $issues
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
			'posi' => Yii::t('common', 'Position'),
			'typesIds' => Yii::t('common', 'Types'),
			'days_reminder' => Yii::t('common', 'Reminder (days)'),
		];
	}

	public function getNameWithShort(): string {
		return $this->name . ' (' . $this->short_name . ')';
	}

	public function getTypesName(): string {
		return implode(', ', $this->types);
	}

	public function getIssues(): IssueQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(Issue::class, ['stage_id' => 'id']);
	}

	public function getTypes(): ActiveQuery {
		return $this->hasMany(IssueType::class, ['id' => 'type_id'])
			->viaTable('{{%issue_stage_type}}', ['stage_id' => 'id']);
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

	/**
	 * @inheritdoc
	 * @return IssueStageQuery the active query used by this AR class.
	 */
	public static function find(): IssueStageQuery {
		return new IssueStageQuery(static::class);
	}

}
