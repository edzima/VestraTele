<?php

namespace common\models\issue;

use common\models\issue\query\IssuePayCalculationQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "issue_pay_calculation".
 *
 * @property int $issue_id
 * @property int $status
 * @property string $value
 * @property int $pay_type
 * @property string $details
 * @property string $created_at
 * @property string $updated_at
 *
 * @property-read string $statusName
 * @property Issue $issue
 */
class IssuePayCalculation extends ActiveRecord {

	public const STATUS_DRAFT = 5;
	public const STATUS_ACTIVE = 10;
	public const STATUS_BEFORE_LAWSUIT = 20;
	public const STATUS_LAWSUIT = 30;
	public const STATUS_BAILIFF = 40;

	public const STATUS_PAYED = 100;

	public const PAY_TYPE_DIRECT = IssuePay::TRANSFER_TYPE_DIRECT;
	public const PAY_TYPE_BANK_TRANSFER = IssuePay::TRANSFER_TYPE_BANK;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return 'issue_pay_calculation';
	}

	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),
			],
		];
	}

	public function afterDelete() {
		IssuePay::deleteAll(['issue_id' => $this->issue_id]);
		parent::afterDelete();
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['created_at', 'updated_at'], 'safe'],
			[['issue_id', 'status', 'value', 'pay_type'], 'required'],
			[['issue_id', 'status', 'pay_type'], 'integer'],
			['value', 'number', 'min' => 1, 'numberPattern' => '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
			[['details'], 'string'],
			[['issue_id'], 'unique'],
			[['pay_type'], 'in', 'range' => array_keys(static::getPayTypesNames())],
			[['status'], 'in', 'range' => array_keys(static::getStatusNames())],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'issue_id' => 'Sprawa',
			'created_at' => 'Dodano',
			'updated_at' => 'Edycja',
			'status' => 'Status',
			'statusName' => 'Status',
			'value' => 'Kwota (Brutto)',
			'pay_type' => 'Preferowany typ płatności',
			'payName' => 'Preferowany typ płatności',
			'details' => 'Szczegóły',
			'payCityDetails' => 'Miasto wypłacające',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssue() {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	public function getStatusName(): string {
		return static::getStatusNames()[$this->status];
	}

	public function getPayName(): string {
		return static::getPayTypesNames()[$this->pay_type];
	}

	public function isActive(): bool {
		return (int) $this->status === static::STATUS_ACTIVE;
	}

	public function isPayed(): bool {
		return (int) $this->status === static::STATUS_PAYED;
	}

	public function isBeforeLawsuit(): bool {
		return (int) $this->status === static::STATUS_BEFORE_LAWSUIT;
	}

	public function isLawsuit(): bool {
		return (int) $this->status === static::STATUS_LAWSUIT;
	}

	public function isBailiff(): bool {
		return (int) $this->status === static::STATUS_BAILIFF;
	}

	public function isDraft(): bool {
		return (int) $this->status === static::STATUS_DRAFT;
	}

	public static function getStatusNames(): array {
		return [
			static::STATUS_DRAFT => 'Pozytywne (Bez terminu)',
			static::STATUS_ACTIVE => 'W trakcie',
			static::STATUS_PAYED => 'Opłacony',
			static::STATUS_BEFORE_LAWSUIT => 'Przygotowanie do sądu',
			static::STATUS_LAWSUIT => 'Sąd',
			static::STATUS_BAILIFF => 'Komornik',
		];
	}

	public static function getPayTypesNames(): array {
		return [
			static::PAY_TYPE_BANK_TRANSFER => 'Przelew',
			static::PAY_TYPE_DIRECT => 'Gotówka',
		];
	}

	public static function find(): IssuePayCalculationQuery {
		return new IssuePayCalculationQuery(static::class);
	}

	public function markAsPayed(): void {
		$this->updateAttributes(['status' => static::STATUS_PAYED]);
	}

}
