<?php

namespace common\modules\lead\models;

use common\modules\lead\Module;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_campaign".
 *
 * @property int $id
 * @property string $name
 * @property int|null $owner_id
 * @property int|null $parent_id
 * @property int|null $sort_index
 *
 * @property-read Lead[] $leads
 * @property-read LeadCampaign $parent
 */
class LeadCampaign extends ActiveRecord {

	private static ?array $models = null;

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_campaign}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['sort_index'], 'integer'],
			[['name'], 'string', 'max' => 255],
			[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['parent_id' => 'id']],
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
			'parent_id' => Yii::t('lead', 'Parent'),
			'owner_id' => Yii::t('lead', 'Owner'),
			'sort_index' => Yii::t('lead', 'Sort Index'),
		];
	}

	/**
	 * Gets query for [[Leads]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getLeads() {
		return $this->hasMany(Lead::class, ['type_id' => 'id']);
	}

	public function getParent(): ActiveQuery {
		return $this->hasOne(static::class, ['parent_id' => 'id']);
	}

	public static function getNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'name');
	}

	/**
	 * @param bool $refresh
	 * @return static[]
	 */
	public static function getModels(bool $refresh = false): array {
		if (static::$models === null || $refresh) {
			static::$models = static::find()
				->indexBy('id')
				->orderBy('sort_index')
				->all();
		}
		return static::$models;
	}
}
