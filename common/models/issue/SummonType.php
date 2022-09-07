<?php

namespace common\models\issue;

use backend\modules\issue\models\SummonForm;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "summon_type".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string|null $title
 * @property string|null $term
 *
 * @property Summon[] $summons
 */
class SummonType extends ActiveRecord {

	/**
	 * @var mixed|null
	 */
	private static array $MODELS;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%summon_type}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'short_name'], 'required'],
			['term', 'integer', 'min' => 1],
			[['name'], 'string', 'max' => 100],
			[['short_name'], 'string', 'max' => 10],
			[['title'], 'string', 'max' => 255],
			[['name'], 'unique'],
			[['short_name'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => Yii::t('common', 'Name'),
			'short_name' => Yii::t('common', 'Short Name'),
			'title' => Yii::t('common', 'Title'),
			'term' => Yii::t('common', 'Term'),
		];
	}

	/**
	 * Gets query for [[Summons]].
	 *
	 * @return ActiveQuery
	 */
	public function getSummons() {
		return $this->hasMany(Summon::class, ['type' => 'id']);
	}

	public function getNameWithShort(): string {
		return $this->short_name . ' - ' . $this->name;
	}

	public static function getNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'name');
	}

	public static function getShortTypesNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'short_name');
	}

	public static function getNamesWithShort(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'nameWithShort');
	}

	/**
	 * @return static[]
	 */
	public static function getModels(): array {
		if (empty(static::$MODELS)) {
			static::$MODELS = static::find()->indexBy('id')->all();
		}
		return static::$MODELS;
	}

}
