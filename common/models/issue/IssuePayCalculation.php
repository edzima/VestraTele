<?php

namespace common\models\issue;

use common\models\issue\query\IssuePayCalculationQuery;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\provision\Provision;
use common\models\provision\ProvisionQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "issue_pay_calculation".
 *
 * @property int $id
 * @property int $issue_id
 * @property int $type
 * @property string $value
 * @property string $details
 * @property string $created_at
 * @property string $updated_at
 * @property string $payment_at
 * @property int $provider_type
 *
 * @property-read Issue $issue
 * @property-read IssuePay[] $pays
 */
class IssuePayCalculation extends ActiveRecord {

	public const TYPE_NOT_SET = 0;
	public const TYPE_ADMINISTRATIVE = 10;
	public const TYPE_PROVISION = 20;
	public const TYPE_PROVISION_REFUND = 25;
	public const TYPE_HONORARIUM = 30;
	public const TYPE_LAWYER = 40;
	public const TYPE_SUBSCRIPTION = 50;

	public const PROVIDER_CLIENT = 1;
	public const PROVIDER_RESPONSIBLE_ENTITY = 10;

	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		$this->issue->markAsUpdate();
	}

	public function afterDelete() {
		parent::afterDelete();
		$this->issue->markAsUpdate();
	}

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

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['created_at', 'updated_at', 'payment_at'], 'safe'],
			[['issue_id', 'value'], 'required'],
			[['issue_id'], 'integer'],
			['value', 'number', 'min' => 1, 'numberPattern' => '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
			[['details'], 'string'],
			[['issue_id'], 'unique'],
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
			'payment_at' => 'Opłacono',
			'value' => 'Kwota (Brutto)',
			'type' => 'Rodzaj',
			'typeName' => 'Typ',
			'details' => 'Szczegóły',
			'providerName' => 'Wpłacający',
		];
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getIssue(): IssueQuery {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getPays(): IssuePayQuery {
		return $this->hasMany(IssuePay::class, ['calculation_id' => 'id']);
	}

	/** @noinspection PhpIncompatibleReturnTypeInspection */
	public function getProvisions(): ProvisionQuery {
		return $this->hasMany(Provision::class, ['pay_id' => 'id'])->via('pays');
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type] ?? '';
	}

	public function isPayed(): bool {
		return !empty($this->payment_at);
	}

	public function getPaysCount(): int {
		return $this->getPays()->count();
	}

	public static function getTypesNames(): array {
		return [
			//static::TYPE_NOT_SET => 'Nieustalono',
			static::TYPE_ADMINISTRATIVE => 'Administracyjne',
			static::TYPE_PROVISION => 'Prowizja',
			static::TYPE_PROVISION_REFUND => 'Zwrot prowizji',
			static::TYPE_HONORARIUM => 'Honorarium',
			static::TYPE_LAWYER => 'Koszty adwokackie',
			static::TYPE_SUBSCRIPTION => 'Abonament',
		];
	}

	/** @noinspection PhpUnused */
	public function getProviderName(): ?string {
		return $this->getProvidersNames()[$this->provider_type] ?? null;
	}

	public function getProvidersNames(): array {
		//@todo change to Client User Model after refactor client.
		return [
			static::PROVIDER_CLIENT => $this->issue->getClientFullName(),
			static::PROVIDER_RESPONSIBLE_ENTITY => $this->issue->entityResponsible,
		];
	}

	public static function find(): IssuePayCalculationQuery {
		return new IssuePayCalculationQuery(static::class);
	}

}
