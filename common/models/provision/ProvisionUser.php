<?php

namespace common\models\provision;

use common\models\user\User;
use Decimal\Decimal;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "provision_user".
 *
 * @property int $id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property int $type_id
 * @property string $value
 * @property string $from_at
 * @property string $to_at
 *
 * @property-read User $fromUser
 * @property-read User $toUser
 * @property ProvisionType $type
 *
 * @property-read string $typeWithValue
 */
class ProvisionUser extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%provision_user}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['from_user_id', 'to_user_id', 'type_id', 'value'], 'required'],
			[['from_user_id', 'to_user_id', 'type_id'], 'integer'],
			[['value'], 'number', 'min' => 0],
			[['from_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['from_user_id' => 'id']],
			[['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['to_user_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProvisionType::class, 'targetAttribute' => ['type_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'from_user_id' => Yii::t('provision', 'From'),
			'to_user_id' => Yii::t('provision', 'To'),
			'type_id' => Yii::t('provision', 'Type'),
			'value' => Yii::t('provision', 'Value'),
			'isOverwritten' => Yii::t('provision', 'Is overwritten'),
			'from_at' => Yii::t('provision', 'From at'),
			'to_at' => Yii::t('provision', 'To at'),
			'fromUser' => Yii::t('provision', 'From'),
			'toUser' => Yii::t('provision', 'To'),
			'formattedValue' => Yii::t('provision', 'Provision'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getFromUser() {
		return $this->hasOne(User::class, ['id' => 'from_user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getToUser() {
		return $this->hasOne(User::class, ['id' => 'to_user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getType() {
		return $this->hasOne(ProvisionType::class, ['id' => 'type_id']);
	}

	public function setType(ProvisionType $type): void {
		$this->type = $type;
		$this->type_id = $type->id;
	}

	public function getIsOverwritten(): bool {
		return $this->getValue()->equals($this->type->getValue());
	}

	public function getTypeWithValue(): string {
		return $this->type->name . ' (' . $this->getFormattedValue() . ')';
	}

	public function getFormattedValue(): string {
		return $this->type->getFormattedValue($this->value);
	}

	public function getValue(): Decimal {
		return new Decimal($this->value);
	}

	public static function find(): ProvisionUserQuery {
		return new ProvisionUserQuery(static::class);
	}

	public function isSelf(): bool {
		return $this->from_user_id === $this->to_user_id;
	}

}
