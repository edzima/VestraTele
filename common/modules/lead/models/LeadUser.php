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
 * @property LeadUserInterface $user
 */
class LeadUser extends ActiveRecord {

	public const TYPE_OWNER = 'owner';
	public const TYPE_DIALER = 'dialer';
	public const TYPE_AGENT = Worker::ROLE_AGENT;
	public const TYPE_TELE = Worker::ROLE_TELEMARKETER;
	public const TYPE_MARKET_FIRST = 'market-first';
	public const TYPE_MARKET_SECOND = 'market-second';
	public const TYPE_MARKET_THIRD = 'market-third';

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

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'user_id' => Yii::t('lead', 'User'),
			'lead_id' => Yii::t('lead', 'Lead'),
			'type' => Yii::t('lead', 'Type'),
			'typeName' => Yii::t('lead', 'Type'),
		];
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	public function getLead(): ActiveQuery {
		return $this->hasOne(Lead::class, ['id' => 'lead_id']);
	}

	public function getUser(): ActiveQuery {
		return $this->hasOne(Module::userClass(), ['id' => 'user_id']);
	}

	public static function userIds(string $type = null): array {
		$query = static::find()
			->select('user_id')
			->distinct();
		if ($type) {
			$query->andWhere([static::tableName() . '.type' => $type]);
		}
		return $query->column();
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_AGENT => Yii::t('lead', 'Agent'),
			static::TYPE_MARKET_FIRST => Yii::t('lead', '{which}. from Market', [
				'which' => 1,
			]),
			static::TYPE_MARKET_SECOND => Yii::t('lead', '{which}. from Market', [
				'which' => 2,
			]),
			static::TYPE_MARKET_THIRD => Yii::t('lead', '{which}. from Market', [
				'which' => 3,
			]),
			static::TYPE_TELE => Yii::t('lead', 'Telemarketer'),
			static::TYPE_OWNER => Yii::t('lead', 'Owner'),
			static::TYPE_DIALER => Yii::t('lead', 'Dialer'),
		];
	}

	public function getUserWithTypeName(): string {
		return $this->user->getFullName() . ' - ' . $this->getTypeName();
	}

}
