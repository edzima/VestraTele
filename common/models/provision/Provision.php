<?php

namespace common\models\provision;

use common\models\issue\IssuePay;
use common\models\user\User;
use Decimal\Decimal;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "provision".
 *
 * @property int $id
 * @property int $pay_id
 * @property string $value
 * @property int $type_id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property bool $hide_on_report
 *
 * @property-read string $provision
 * @property-read IssuePay $pay
 * @property-read ProvisionType $type
 * @property-read User $toUser
 * @property-read User $fromUser
 */
class Provision extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'provision';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['pay_id', 'to_user_id', 'value', 'type_id'], 'required'],
			[['pay_id', 'to_user_id', 'from_user_id'], 'integer'],
			[['value'], 'number'],
			['hide_on_report', 'boolean'],
			[['pay_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssuePay::class, 'targetAttribute' => ['pay_id' => 'id']],
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
			'pay_id' => 'Pay ID',
			'to_user_id' => 'Dla',
			'from_user_id' => 'Nadprowizja',
			'value' => 'Honorarium (netto)',
			'toUser' => 'Dla',
			'fromUser' => 'Nadprowizja',
			'fromUserString' => 'Nadprowizja',
			'provision' => 'Prowizja (%)',
			'hide_on_report' => 'Ukryty w raporcie',
		];
	}

	public function getPay(): ActiveQuery {
		return $this->hasOne(IssuePay::class, ['id' => 'pay_id']);
	}

	public function getFromUser(): ActiveQuery {
		return $this->hasOne(User::class, ['id' => 'from_user_id']);
	}

	public function getToUser(): ActiveQuery {
		return $this->hasOne(User::class, ['id' => 'to_user_id']);
	}

	public function getType(): ActiveQuery {
		return $this->hasOne(ProvisionType::class, ['id' => 'type_id']);
	}

	public function getFromUserString(): string {
		return $this->from_user_id
		&& $this->to_user_id !== $this->from_user_id
		&& $this->fromUser ? $this->fromUser : '';
	}

	public function getValue(): Decimal {
		return new Decimal($this->value);
	}

	public function getProvision(): string {
		return $this->getProvisionDecimal()->toFixed(2);
	}

	public function getProvisionDecimal(): Decimal {
		return $this->getValue()
			->div($this->pay->getValueWithoutVAT());
	}

	public static function find(): ProvisionQuery {
		return new ProvisionQuery(static::class);
	}
}
