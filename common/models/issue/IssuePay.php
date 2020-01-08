<?php

namespace common\models\issue;

use common\models\provision\ProvisionQuery;
use Yii;
use yii\db\ActiveRecord;
use common\models\provision\Provision;

/**
 * This is the model class for table "issue_pay".
 *
 * @property int $id
 * @property int $issue_id
 * @property string $pay_at
 * @property string $deadline_at
 * @property string $value
 * @property int $type
 * @property int $transfer_type
 * @property string $vat
 *
 * @property-read float $valueNetto
 *
 * @property-read Issue $issue
 * @property-read IssuePay[] $pays
 * @property-read Provision[] $provisions
 */
class IssuePay extends ActiveRecord {

	public const TYPE_HONORARIUM = 1;
	public const TYPE_COMPENSTAION = 2;

	public const TRANSFER_TYPE_DIRECT = 1;
	public const TRANSFER_TYPE_BANK = 2;

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return 'issue_pay';
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['issue_id', 'type', 'value', 'deadline_at', 'transfer_type', 'vat'], 'required', 'enableClientValidation' => false],
			[['issue_id', 'type', 'transfer_type'], 'integer'],
			[['pay_at', 'deadline_at'], 'safe'],
			[['value', 'vat'], 'number'],
			['vat', 'number', 'min' => 0, 'max' => 100],
			[['type'], 'in', 'range' => array_keys(static::getTypesNames())],
			[['transfer_type'], 'in', 'range' => array_keys(static::getTransferTypesNames())],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
		];
	}

	public function afterSave($insert, $changedAttributes): void {
		$this->issue->markAsUpdate();
		if ($this->isPayed() && (int) $this->issue->getPays()->onlyNotPayed()->count() === 0) {
			$this->issue->payCalculation->markAsPayed();
		}
		parent::afterSave($insert, $changedAttributes);
	}

	public function afterDelete(): void {
		$this->issue->markAsUpdate();
		parent::afterDelete();
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'issue_id' => 'Sprawa',
			'pay_at' => 'Data płatności',
			'deadline_at' => 'Termin płatności',
			'value' => 'Honorarium (Brutto)',
			'valueNetto' => 'Honorarium (Netto)',
			'type' => 'Rodzaj',
			'transfer_type' => 'Przelew/konto',
			'partInfo' => 'Część',
			'vat' => 'VAT (%)',
			'vatPercent' => 'VAT (%)',
		];
	}

	public function getValueNetto(): float {
		return Yii::$app->tax->netto($this->value, $this->vat);
	}

	public function getVatPercent(): float {
		return $this->vat / 100;
	}

	public function getIssue(): IssueQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	public function getProvisions(): ProvisionQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(Provision::class, ['pay_id' => 'id']);
	}

	public function getCityDate(): ?string {
		$details = $this->issue->payCity;
		if ($details === null) {
			return null;
		}
		switch ($this->transfer_type) {
			case static::TRANSFER_TYPE_DIRECT:
				return $details->direct_at;
			case static::TRANSFER_TYPE_BANK:
				return $details->bank_transfer_at;
			default:
				return null;
		}
	}

	public function isPayed(): bool {
		return $this->pay_at > 0;
	}

	public function getPartInfo(): string {
		$pays = $this->issue->pays;
		$count = count($pays);
		if ($count === 1) {
			return '1/1';
		}
		$i = 0;
		foreach ($pays as $pay) {
			$i++;
			if ($pay->id === $this->id) {
				return $i . '/' . $count;
			}
		}
		return '';
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	public function getTransferTypeName(): string {
		return static::getTransferTypesNames()[$this->transfer_type];
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_HONORARIUM => 'Honorarium',
			static::TYPE_COMPENSTAION => 'Odszkodowanie',
		];
	}

	public static function getTransferTypesNames(): array {
		return [
			static::TRANSFER_TYPE_BANK => 'Przelew',
			static::TRANSFER_TYPE_DIRECT => 'Gotówka',
		];
	}

	public static function find(): IssuePayQuery {
		return new IssuePayQuery(static::class);
	}

}
