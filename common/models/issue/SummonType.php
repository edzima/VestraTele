<?php

namespace common\models\issue;

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
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%summon_type}}';
	}

	public static function getNames(): array {
		return ArrayHelper::map(
			static::find()
				->all(),
			'id', 'name');
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'short_name'], 'required'],
			['term', 'integer'],
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
}
