<?php

namespace common\modules\lead\models;

use common\helpers\ArrayHelper;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "lead_crm".
 *
 * @property int $id
 * @property string|null $name
 * @property string $backend_url
 * @property string $frontend_url
 *
 * @property LeadIssue[] $leadIssues
 */
class LeadCrm extends ActiveRecord {

	private static $MODELS = [];

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_crm}}';
	}

	public static function getNames(): array {
		return ArrayHelper::map(static::getModels(), 'id', 'name');
	}

	public static function getModels(): array {
		if (empty(static::$MODELS)) {
			static::$MODELS = static::find()->all();
		}
		return static::$MODELS;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['backend_url', 'frontend_url'], 'required'],
			[['name', 'backend_url', 'frontend_url'], 'string', 'max' => 255],
			[['backend_url'], 'unique'],
			[['frontend_url'], 'unique'],
			[['frontend_url', 'backend_url'], 'url'],
			[['name'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'name' => Yii::t('lead', 'Name'),
			'backend_url' => Yii::t('lead', 'Backend Url'),
			'frontend_url' => Yii::t('lead', 'Frontend Url'),
		];
	}

	public function getLeadIssues(): ActiveQuery {
		return $this->hasMany(LeadIssue::class, ['crm_id' => 'id']);
	}
}
