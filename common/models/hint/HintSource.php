<?php

namespace common\models\hint;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "hint_source".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int|null $is_active
 *
 * @property HintCitySource[] $hintCitySources
 * @property HintCity[] $hints
 */
class HintSource extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%hint_source}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'short_name'], 'required'],
			[['is_active'], 'integer'],
			[['name'], 'string', 'max' => 255],
			[['name'], 'unique'],
			[['short_name'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('hint', 'ID'),
			'name' => Yii::t('hint', 'Name'),
			'short_name' => Yii::t('hint', 'Short name'),
			'is_active' => Yii::t('hint', 'Is Active'),
		];
	}

	public function getHintCitySources(): ActiveQuery {
		return $this->hasMany(HintCitySource::class, ['source_id' => 'id']);
	}

	public function getHints(): ActiveQuery {
		return $this->hasMany(HintCity::class, ['id' => 'hint_id'])->viaTable('hint_city_source', ['source_id' => 'id']);
	}
}
