<?php

namespace common\models\issue;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "summon_doc".
 *
 * @property int $id
 * @property string $name
 * @property int|null $priority
 * @property string|null $done_at
 *
 * @property Summon[] $summons
 * @property-read string|null $priorityName
 */
class SummonDoc extends ActiveRecord {

	public const PRIORITY_LOW = 1;
	public const PRIORITY_MEDIUM = 5;
	public const PRIORITY_HIGH = 10;

	public function getPriorityName(): ?string {
		return static::getPriorityNames()[$this->priority];
	}

	public static function getPriorityNames(): array {
		return [
			static::PRIORITY_HIGH => Yii::t('common', 'High'),
			static::PRIORITY_MEDIUM => Yii::t('common', 'Medium'),
			static::PRIORITY_LOW => Yii::t('common', 'Low'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%summon_doc}}';
	}

	public static function getNames(): array {
		return ArrayHelper::map(static::find()->all(), 'id', 'name');
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['name'], 'string', 'max' => 255],
			[['name'], 'unique'],
			['done_at', 'safe'],
			['priority', 'integer'],
			['priority', 'in', 'range' => array_keys(static::getPriorityNames())],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('common', 'ID'),
			'name' => Yii::t('common', 'Name'),
			'priority' => Yii::t('common', 'Priority'),
			'priorityName' => Yii::t('common', 'Priority'),
		];
	}

	/**
	 * Gets query for [[Summons]].
	 *
	 * @return ActiveQuery
	 */
	public function getSummons() {
		return $this->hasMany(Summon::class, ['id' => 'summon_id'])->via(SummonDocLink::class, ['doc_type_id' => 'id']);
	}

	public function getLinks() {
		return $this->hasMany(SummonDocLink::class, ['doc_type_id' => 'id']);
	}

	public static function find() {
		return parent::find()
			->orderBy([
				static::tableName() . '.priority' => SORT_DESC,
				static::tableName() . '.name' => SORT_ASC,
			]);
	}

}
