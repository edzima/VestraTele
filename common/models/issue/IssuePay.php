<?php

namespace common\models\issue;

use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\provision\Provision;
use common\models\provision\ProvisionQuery;
use common\models\settlement\Pay;
use common\models\settlement\PayInterface;
use common\models\settlement\VATInfoTrait;
use DateTime;
use Decimal\Decimal;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_pay".
 *
 * @property int $id
 * @property int $calculation_id
 * @property string $pay_at
 * @property string $deadline_at
 * @property string $value
 * @property string|null $transfer_type
 * @property string $vat
 * @property int $status
 *
 * @todo replace with valueWithoutVAT
 * @property-read float $valueNetto
 *
 * @property-read Issue $issue
 * @property-read Provision[] $provisions
 * @property-read IssuePayCalculation $calculation
 */
class IssuePay extends ActiveRecord implements IssuePayInterface {

	use VATInfoTrait;

	public const DATE_FORMAT = 'Y-m-d';

	public const STATUS_INFORMED = 1;
	public const STATUS_NO_CONTACT = 10;
	public const STATUS_REQUEST_PAY = 30;
	public const STATUS_REQUEST_REDUCTION = 40;
	public const STATUS_ANALYSE = 50;

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%issue_pay}}';
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
			[
				['status'], 'in', 'range' => array_keys(static::getStatusNames()), 'when' => function (): bool {
				return !empty($this->status);
			},
			],
			[['transfer_type'], 'in', 'range' => array_keys(static::getTransfersTypesNames())],
			[['calculation_id'], 'exist', 'skipOnError' => true, 'targetClass' => IssuePayCalculation::class, 'targetAttribute' => ['calculation_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'pay_at' => Yii::t('settlement', 'Pay at'),
			'deadline_at' => Yii::t('settlement', 'Deadline at'),
			'value' => Yii::t('settlement', 'Value with VAT'),
			'valueNetto' => Yii::t('settlement', 'Value without VAT'),
			'transfer_type' => Yii::t('settlement', 'Transfer Type'),
			'partInfo' => Yii::t('settlement', 'Part Info'),
			'vat' => Yii::t('settlement', 'VAT (%)'),
			'vatPercent' => Yii::t('settlement', 'VAT (%)'),
		];
	}

	public function getId(): int {
		return $this->id;
	}

	public function isPayed(): bool {
		return $this->getPaymentAt() !== null;
	}

	public function isDelayed(string $range = 'now'): bool {
		if ($this->isPayed() || $this->getDeadlineAt() === null) {
			return false;
		}
		return new DateTime($range) > $this->getDeadlineAt();
	}

	public function setPay(PayInterface $pay): void {
		$this->value = $pay->getValue()->toFixed(2);
		$this->vat = $pay->getVAT() ? $pay->getVAT()->toFixed(2) : null;
		$this->pay_at = $pay->getPaymentAt() ? $pay->getPaymentAt()->format(static::DATE_FORMAT) : null;
		$this->deadline_at = $pay->getDeadlineAt() ? $pay->getDeadlineAt()->format(static::DATE_FORMAT) : null;
		$this->transfer_type = $pay->getTransferType();
	}

	public function getDeadlineAt(): ?DateTime {
		try {
			return $this->deadline_at ? new DateTime($this->deadline_at) : null;
		} catch (Exception $e) {
			Yii::warning($e->getMessage(), 'pays.deadline_at');

			return null;
		}
	}

	public function getValue(): Decimal {
		return new Decimal($this->value);
	}

	public function getPaymentAt(): ?DateTime {
		try {
			return $this->pay_at ? new DateTime($this->pay_at) : null;
		} catch (Exception $e) {
			Yii::warning($e->getMessage(), 'pays.pay_at');
			return null;
		}
	}

	public function getTransferType(): ?string {
		return $this->transfer_type;
	}

	public function getCalculation(): ActiveQuery {
		return $this->hasOne(IssuePayCalculation::class, ['id' => 'calculation_id']);
	}

	public function getValueNetto(): string {
		return Yii::$app->tax->netto(new Decimal($this->value), new Decimal($this->vat))->toFloat();
	}

	public function getIssue(): IssueQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasOne(Issue::class, ['id' => 'issue_id'])->via('calculation');
	}

	public function getProvisions(): ProvisionQuery {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->hasMany(Provision::class, ['pay_id' => 'id']);
	}

	public function getPartInfo(): string {
		$index = $this->getSettlementPartIndex();
		if ($index === 1) {
			return '1/1';
		}
		return $index . '/' . count($this->calculation->pays);
	}

	public function getSettlementPartIndex(): int {
		if ($this->getValue()->equals($this->calculation->getValue())
			|| count($this->calculation->pays) === 1) {
			return 1;
		}
		$pays = $this->calculation->pays;
		$i = 0;
		foreach ($pays as $pay) {
			$i++;
			if ($pay->id === $this->id) {
				break;
			}
		}
		return $i;
	}

	public function getStatusName(): ?string {
		return static::getStatusNames()[$this->status];
	}

	public function getTransferTypeName(): string {
		return static::getTransfersTypesNames()[$this->transfer_type];
	}

	public function markAsPaid(DateTime $date, string $transfer_type): bool {
		$this->pay_at = $date->format('Y-m-d');
		$this->transfer_type = $transfer_type;
		$this->status = null;
		return $this->save(false);
	}

	public static function getStatusNames(): array {
		return [
			static::STATUS_INFORMED => Yii::t('settlement', 'Informed'),
			static::STATUS_NO_CONTACT => Yii::t('settlement', 'No contact'),
			static::STATUS_REQUEST_PAY => Yii::t('settlement', 'Pay request'),
			static::STATUS_REQUEST_REDUCTION => Yii::t('settlement', 'Reduction request'),
			static::STATUS_ANALYSE => Yii::t('settlement', 'Analyse'),
		];
	}

	public static function getTransfersTypesNames(): array {
		return Pay::getTransfersTypesNames();
	}

	public static function find(): IssuePayQuery {
		return new IssuePayQuery(static::class);
	}

	public function getSettlementId(): int {
		return $this->calculation_id;
	}
}
