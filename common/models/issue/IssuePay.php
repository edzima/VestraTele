<?php

namespace common\models\issue;

use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\provision\ProvisionQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use common\models\provision\Provision;

/**
 * This is the model class for table "issue_pay".
 *
 * @property int $id
 * @property int $calculation_id
 * @property string $pay_at
 * @property string $deadline_at
 * @property string $value
 * @property int $transfer_type
 * @property string $vat
 * @property int $status
 *
 * @property-read float $valueNetto
 *
 * @property-read Issue $issue
 * @property-read IssuePay[] $pays
 * @property-read Provision[] $provisions
 * @property-read IssuePayCalculation $calculation
 */
class IssuePay extends ActiveRecord {

	public const TRANSFER_TYPE_DIRECT = 1;
	public const TRANSFER_TYPE_BANK = 2;

	public const STATUS_NO_PROBLEM = 0;
	public const STATUS_PROBLEM = 10;
	public const STATUS_PRE_JUDGMENT = 20;
	public const STATUS_JUDGMENT = 30;

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
			[['value', 'deadline_at', 'transfer_type', 'vat'], 'required', 'enableClientValidation' => false],
			[['transfer_type'], 'integer'],
			[['pay_at', 'deadline_at'], 'safe'],
			[['value', 'vat'], 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/', 'enableClientValidation' => false],
			['vat', 'number', 'min' => 0, 'max' => 100],
			[['status'], 'in', 'range' => array_keys(static::getStatusNames())],
			[['transfer_type'], 'in', 'range' => array_keys(static::getTransferTypesNames())],
			[['calculation_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssuePayCalculation::class, 'targetAttribute' => ['calculation_id' => 'id']],
		];
	}

	//@todo move to PayController
	public function afterSave($insert, $changedAttributes): void {
		if ($this->isPayed() && (int) $this->calculation->getPays()->onlyNotPayed()->count() === 0) {
			//	$this->calculation->markAsPayed();
		}
		parent::afterSave($insert, $changedAttributes);
	}

	public function afterDelete(): void {
		parent::afterDelete();
		//$this->calculation->update();
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'pay_at' => 'Data płatności',
			'deadline_at' => 'Termin płatności',
			'value' => 'Honorarium (Brutto)',
			'valueNetto' => 'Honorarium (Netto)',
			'transfer_type' => 'Przelew/konto',
			'partInfo' => 'Część',
			'vat' => 'VAT (%)',
			'vatPercent' => 'VAT (%)',
		];
	}

	public function getCalculation(): ActiveQuery {
		return $this->hasOne(IssuePayCalculation::class, ['id' => 'calculation_id']);
	}

	public function getValueNetto(): float {
		return Yii::$app->tax->netto($this->value, $this->vat);
	}

	public function getVatPercent(): float {
		return $this->vat / 100;
	}

	public function getIssue(): IssueQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(Issue::class, ['id' => 'issue_id'])->via('calculation');
	}

	public function getProvisions(): ProvisionQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(Provision::class, ['pay_id' => 'id']);
	}

	public function isPayed(): bool {
		return $this->pay_at > 0;
	}

	public function getPartInfo(): string {
		$pays = $this->calculation->pays;
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

	public function getStatusName(): string {
		return static::getStatusNames()[$this->status];
	}

	public function getTransferTypeName(): string {
		return static::getTransferTypesNames()[$this->transfer_type];
	}

	public static function getTransferTypesNames(): array {
		return [
			static::TRANSFER_TYPE_BANK => 'Przelew',
			static::TRANSFER_TYPE_DIRECT => 'Gotówka',
		];
	}

	public static function getStatusNames(): array {
		return [
			static::STATUS_NO_PROBLEM => 'Brak',
			static::STATUS_PROBLEM => 'Problem',
			static::STATUS_PRE_JUDGMENT => 'Przygotowanie do sądu',
			static::STATUS_JUDGMENT => 'Sąd',
		];
	}

	public static function find(): IssuePayQuery {
		return new IssuePayQuery(static::class);
	}

	/**
	 * @param static[] $pays
	 * @return static[]
	 */
	public static function payedFilter(array $pays): array {
		return array_filter($pays, static function (IssuePay $pay) {
			return $pay->isPayed();
		});
	}

}
