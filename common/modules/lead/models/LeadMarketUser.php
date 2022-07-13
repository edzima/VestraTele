<?php

namespace common\modules\lead\models;

use common\modules\lead\Module;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "lead_market_user".
 *
 * @property int $id
 * @property int $market_id
 * @property int $user_id
 * @property int $status
 * @property int $days_reservation
 * @property string|null $details
 * @property string|null $reserved_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property LeadMarket $market
 * @property LeadUserInterface $user
 */
class LeadMarketUser extends ActiveRecord {

	public const STATUS_TO_CONFIRM = 1;
	public const STATUS_ACCEPTED = 5;
	public const STATUS_REJECTED = 2;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_market_user}}';
	}

	public static function getStatusesNames(): array {
		return [
			static::STATUS_TO_CONFIRM => Yii::t('lead', 'To Confirm'),
			static::STATUS_ACCEPTED => Yii::t('lead', 'Accepted'),
			static::STATUS_REJECTED => Yii::t('lead', 'Rejected'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('NOW()'),
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['market_id', 'status', 'user_id'], 'required'],
			[['market_id', 'status', 'user_id', 'days_reservation'], 'integer'],
			['days_reservation', 'integer', 'min' => 1],
			[['details'], 'string'],
			[['market_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadMarket::class, 'targetAttribute' => ['market_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::userClass(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'market_id' => Yii::t('lead', 'Market ID'),
			'user_id' => Yii::t('lead', 'User ID'),
			'status' => Yii::t('lead', 'Status'),
			'created_at' => Yii::t('lead', 'Created At'),
			'updated_at' => Yii::t('lead', 'Updated At'),
			'days_reservation' => Yii::t('lead', 'Days Reservation'),
			'reserved_at' => Yii::t('lead', 'Reserved At'),
		];
	}

	public function getMarket(): ActiveQuery {
		return $this->hasOne(LeadMarket::class, ['id' => 'market_id']);
	}

	public function getUser(): ActiveQuery {
		return $this->hasOne(Module::userClass(), ['id' => 'user_id']);
	}

	public function isToConfirm(): bool {
		//@todo maybe or with status Rejected.
		return $this->status === LeadMarketUser::STATUS_TO_CONFIRM;
	}

	public function isAccepted(): bool {
		return $this->status === static::STATUS_ACCEPTED;
	}

	public function isRejected(): bool {
		return $this->status === static::STATUS_REJECTED;
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	public function accept(bool $update = true): void {
		$this->status = static::STATUS_ACCEPTED;
		$this->generateReservedAt();
		if ($update) {
			$this->updateAttributes([
				'status',
				'reserved_at',
			]);
		}
		$this->market->bookIt($update);
	}

	public function reject(bool $update = true): void {
		$this->status = static::STATUS_REJECTED;
		$this->reserved_at = null;
		if ($update) {
			$this->updateAttributes([
				'status',
				'reserved_at',
			]);
		}
	}

	public function generateReservedAt(int $baseTime = null): void {
		if ($this->days_reservation > 0) {
			if ($baseTime === null) {
				$baseTime = time();
			}
			$this->reserved_at = date('Y-m-d', strtotime("+ $this->days_reservation days", $baseTime));
		} else {
			$this->reserved_at = null;
		}
	}

}
