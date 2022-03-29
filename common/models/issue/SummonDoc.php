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
 *
 * @property Summon[] $summons
 */
class SummonDoc extends ActiveRecord {

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
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('common', 'ID'),
			'name' => Yii::t('common', 'Name'),
		];
	}

	/**
	 * Gets query for [[Summons]].
	 *
	 * @return ActiveQuery
	 */
	public function getSummons() {
		return $this->hasMany(Summon::class, ['doc_type_id' => 'id']);
	}
}
