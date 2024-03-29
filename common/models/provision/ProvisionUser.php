<?php

namespace common\models\provision;

use common\models\user\User;
use DateTime;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;
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
			'fromUserNameWhenNotSelf' => Yii::t('provision', 'From'),
			'formattedValue' => Yii::t('provision', 'Provision'),
		];
	}

	public function getFromUserNameWhenNotSelf(): ?string {
		if ($this->isSelf()) {
			return null;
		}
		return $this->fromUser->getFullName();
	}

	/**
	 * @return ActiveQuery
	 */
	public function getFromUser() {
		return $this->hasOne(User::class, ['id' => 'from_user_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getToUser() {
		return $this->hasOne(User::class, ['id' => 'to_user_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getType() {
		return $this->hasOne(ProvisionType::class, ['id' => 'type_id']);
	}

	public function setType(ProvisionType $type): void {
		$this->type = $type;
		$this->type_id = $type->id;
		if ($this->value === null) {
			$this->value = $type->value;
		}
	}

	public function getIsOverwritten(): bool {
		return $this->getValue()->equals($this->type->getValue());
	}

	public function getTypeWithValue(): string {
		return $this->type->name . ' (' . $this->getFormattedValue() . ')';
	}

	public function getFormattedValue(): string {
		return $this->type->getFormattedValue($this->getValue());
	}

	public function getValue(): Decimal {
		return new Decimal($this->value);
	}

	public function isSelf(): bool {
		return $this->from_user_id === $this->to_user_id;
	}

	public function isForDate($date): bool {
		if (empty($this->from_at) && empty($this->to_at)) {
			return true;
		}
		if (!$date instanceof DateTime) {
			$date = new DateTime($date);
		}
		if (!empty($this->from_at)) {
			$fromAt = new DateTime($this->from_at);
			if (empty($this->to_at)) {
				return $date >= $fromAt;
			}
			return $date >= $fromAt && $date <= new DateTime($this->to_at);
		}
		return $date <= new DateTime($this->to_at);
	}

	/**
	 * @param Decimal|null $value
	 * @return Decimal
	 */
	public function generateProvision(Decimal $value = null): Decimal {
		if (!$this->type->is_percentage) {
			return $this->getValue();
		}
		if ($value === null) {
			throw new InvalidArgumentException('$value must be Decimal when type is percentage.');
		}
		return $value->mul($this->getValue())->div(100);
	}

	public static function find(): ProvisionUserQuery {
		return new ProvisionUserQuery(static::class);
	}

	public static function createFromBaseType(ProvisionUser $model, ProvisionType $type): self {
		$self = new static();
		$self->from_at = $model->from_at;
		$self->to_at = $model->to_at;
		$self->from_user_id = $model->from_user_id;
		$self->to_user_id = $model->to_user_id;
		$self->setType($type);
		$self->value = static::addValue($model, $self)->toFixed(2);
		return $self;
	}

	public static function addValue(ProvisionUser $a, ProvisionUser $b): Decimal {
		if ($a->type->is_percentage && !$b->type->is_percentage) {
			throw new InvalidArgumentException('Percentage/Const of Type must be same.');
		}
		$type = $a->type;
		$valueA = $a->getValue();
		$valueB = $b->getValue();
		if (!$type->is_percentage) {
			return $valueA->add($valueB);
		}
		return $valueA->mul($valueB)->div(100);
	}
}
