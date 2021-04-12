<?php

namespace common\modules\lead\models;

use common\modules\lead\Module;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_source".
 *
 * @property int $id
 * @property string $name
 * @property string|null $url
 * @property int|null $sort_index
 * @property int|null $owner_id
 *
 * @property-read  Lead[] $leads
 */
class LeadSource extends ActiveRecord {

	private static ?array $models = null;

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_source}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['sort_index'], 'integer'],
			[['name', 'url'], 'string', 'max' => 255],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['owner_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'name' => Yii::t('lead', 'Name'),
			'url' => Yii::t('lead', 'URL'),
			'sort_index' => Yii::t('lead', 'Sort Index'),
		];
	}

	/**
	 * Gets query for [[Leads]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getLeads() {
		return $this->hasMany(Lead::class, ['source_id' => 'id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return ActiveQuery
	 */
	public function getOwner(): ActiveQuery {
		return $this->hasOne(Module::userClass(), ['owner_id' => 'id']);
	}

	public static function getNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'name');
	}

	public static function getModels(): array {
		if (static::$models === null) {
			static::$models = static::find()
				->indexBy('id')
				->orderBy('sort_index')
				->all();
		}
		return static::$models;
	}
}
