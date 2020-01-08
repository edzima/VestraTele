<?php

namespace common\models\provision;

use common\models\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "provision_user".
 *
 * @property int $from_user_id
 * @property int $to_user_id
 * @property int $type_id
 * @property string $value
 *
 * @property User $fromUser
 * @property User $toUser
 * @property ProvisionType $type
 *
 * @property-read string $typeWithValue
 */
class ProvisionUser extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return 'provision_user';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['from_user_id', 'to_user_id', 'type_id', 'value'], 'required'],
			[['from_user_id', 'to_user_id', 'type_id'], 'integer'],
			[['value'], 'number', 'min' => 0],
			[['from_user_id', 'to_user_id', 'type_id'], 'unique', 'targetAttribute' => ['from_user_id', 'to_user_id', 'type_id']],
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
			'from_user_id' => 'Od',
			'to_user_id' => 'Do',
			'type_id' => 'Typ',
			'value' => 'Prowizja',
			'isDefaultValue' => 'Domyślna prowizja',
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

	public function getIsDefaultValue(): bool {
		return $this->value === $this->type->value;
	}

	public function getTypeWithValue(): string {
		return $this->type->name . ' (' . $this->getFormattedValue() . ')';
	}

	public function getFormattedValue(): string {
		return $this->type->getFormattedValue($this->value);
	}


	public static function find(): ProvisionUserQuery {
		return new ProvisionUserQuery(static::class);
	}

}
