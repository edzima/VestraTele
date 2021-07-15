<?php

namespace common\modules\lead\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_type".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $sort_index
 *
 * @property Lead[] $leads
 */
class LeadType extends ActiveRecord implements LeadTypeInterface {

	private static ?array $models = null;

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_type}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['sort_index'], 'integer'],
			[['name', 'description'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'name' => Yii::t('lead', 'Name'),
			'description' => Yii::t('lead', 'Description'),
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

	public static function getNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'name');
	}

	/**
	 * @param bool $refresh
	 * @return static[]
	 */
	public static function getModels(bool $refresh = false): array {
		if (empty(static::$models) || $refresh) {
			static::$models = static::find()
				->indexBy('id')
				->orderBy('sort_index')
				->all();
		}
		return static::$models;
	}

	public function getID(): int {
		return $this->id;
	}

	public function getName(): string {
		return $this->name;
	}
}
