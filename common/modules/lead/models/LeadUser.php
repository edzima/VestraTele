<?php

namespace common\modules\lead\models;

use common\models\user\Worker;
use common\modules\lead\Module;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_user".
 *
 * @property int $user_id
 * @property int $lead_id
 * @property string $type
 *
 * @property Lead $lead
 * @property Worker $user
 */
class LeadUser extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_user}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'lead_id', 'type'], 'required'],
			[['user_id', 'lead_id'], 'integer'],
			[['type'], 'string', 'max' => 255],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			[['user_id', 'lead_id', 'type'], 'unique', 'targetAttribute' => ['user_id', 'lead_id', 'type']],
			[['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function getTypeWithUser(): string {
		return $this->getTypeName() . ' - ' . $this->user->getFullName();
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'user_id' => Yii::t('lead', 'User'),
			'lead_id' => Yii::t('common', 'Lead'),
			'type' => Yii::t('common', 'Type'),
		];
	}

	public function getLead(): ActiveQuery {
		return $this->hasOne(Lead::class, ['id' => 'lead_id']);
	}

	public function getUser(): ActiveQuery {
		return $this->hasOne(Module::userClass(), ['id' => 'user_id']);
	}

	public static function userIds(string $type): array {
		return static::find()
			->select('user_id')
			->distinct()
			->andWhere([static::tableName() . '.type' => $type])
			->column();
	}

}
