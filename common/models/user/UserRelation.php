<?php

namespace common\models\user;

use common\models\relation\RelationModel;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_relation".
 *
 * @property int $user_id
 * @property int $to_user_id
 * @property string $type
 * @property int $created_at
 * @property int $updated_at
 *
 * @property-read User $toUser
 * @property-read User $user
 */
class UserRelation extends ActiveRecord implements RelationModel {

	public const TYPE_SUPERVISOR = 'supervisior';
	public const TYPE_PREVIEW_ISSUES = 'preview.issues';

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
		return [
			TimestampBehavior::class,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%user_relation}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'to_user_id', 'type'], 'required'],
			[['user_id', 'to_user_id', 'created_at', 'updated_at'], 'integer'],
			[['type'], 'string', 'max' => 255],
			[['user_id', 'to_user_id', 'type'], 'unique', 'targetAttribute' => ['user_id', 'to_user_id', 'type']],
			[['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => static::toTargetClass(), 'targetAttribute' => ['to_user_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => static::fromTargetClass(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'user_id' => Yii::t('common', 'User ID'),
			'user' => Yii::t('common', 'User'),
			'toUser' => Yii::t('common', 'To User'),
			'to_user_id' => Yii::t('common', 'To User ID'),
			'type' => Yii::t('common', 'Type'),
			'typeName' => Yii::t('common', 'Type'),
			'created_at' => Yii::t('common', 'Created At'),
			'updated_at' => Yii::t('common', 'Updated At'),
		];
	}

	/**
	 * Gets query for [[ToUser]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getToUser() {
		return $this->hasOne(User::class, ['id' => 'to_user_id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_SUPERVISOR => Yii::t('common', 'Supervisor'),
			static::TYPE_PREVIEW_ISSUES => Yii::t('common', 'Preview Issues'),
		];
	}

	public function getFromId(): int {
		return $this->user_id;
	}

	public function getToId(): int {
		return $this->to_user_id;
	}

	public function getType(): string {
		return $this->type;
	}

	public static function fromAttribute(): string {
		return 'user_id';
	}

	public static function toAttribute(): string {
		return 'to_user_id';
	}

	public static function typeAttribute(): string {
		return 'type';
	}

	public static function fromTargetClass(): string {
		return User::class;
	}

	public static function toTargetClass(): string {
		return User::class;
	}
}
