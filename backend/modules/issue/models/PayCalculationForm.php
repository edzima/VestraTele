<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssuePayCity;
use DateTime;
use Yii;
use yii\base\Model;

class PayCalculationForm extends Model {

	public const GENERATE_NAME = 'generate';

	public $value = 4815;
	public $payParts = 1;
	public $firstBillDate;
	public $payAt;

	public $dateInterval = '+ 3 days';
	public $dateFormat = DATE_ATOM;

	/** @var Issue */
	private $issue;
	/** @var IssuePayCalculation */
	private $calculation;
	/** @var IssuePay[] */
	private $pays = [];
	/** @var IssuePayCity */
	private $payCity;
	private $_isGenerate = false;

	public function __construct(Issue $issue, $config = []) {
		$this->issue = $issue;
		$this->calculation = $this->getIssue()->payCalculation ?? $this->createPayCalculationModel();
		parent::__construct($config);
	}

	public function attributeLabels(): array {
		return [
			'payParts' => 'Ilość rat',
			'firstBillDate' => 'Termin płatności',
			'payAt' => 'Data płatności',
			'value' => 'Wartość',
		];
	}

	public function rules(): array {
		return [
			[['value'], 'required'],
			[
				'value', 'validateValue', 'when' => function () {
				return $this->payParts > 0;
			},
			],
			['value', 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/', 'enableClientValidation' => false],
			['payParts', 'integer', 'min' => 0, 'max' => 10],
			['firstBillDate', 'date', 'format' => DATE_ATOM],
			['payAt', 'date', 'format' => DATE_ATOM],
		];
	}

	public function setValue($value): void {
		$this->value = str_replace(',', '.', $value);
	}

	public function validateValue($attribute, $params, $validator): void {
		$value = (float) $this->$attribute;
		$sum = $this->getPaysSumValue();
		if ($value !== $sum) {
			$formatter = Yii::$app->formatter;
			$this->addError($attribute, 'Suma rat musi być: ' . $formatter->asDecimal($value) . '. Wynosi obecnie: ' . $formatter->asDecimal($sum) . '.');
		}
	}

	public function isGenerate(): bool {
		return $this->_isGenerate;
	}

	public function getIssue(): Issue {
		return $this->issue;
	}

	private function createPayCalculationModel(): IssuePayCalculation {
		$model = new IssuePayCalculation();
		$model->issue_id = $this->getIssue()->id;
		$model->status = IssuePayCalculation::STATUS_ACTIVE;
		$model->value = $this->getIssue()->provision_base;
		return $model;
	}

	public function init(): void {
		if ($this->calculation->value > 0) {
			$this->value = $this->calculation->value;
		}
		$this->firstBillDate = $this->getDefaultFirstBillDate();

		if ($this->isCreateForm()) {
			$this->getPayCalculation()->pay_type = $this->getDefaultPayType();
			$this->pays = $this->generatePays();
		} else {
			$this->pays = $this->getIssue()->pays;
			$this->payParts = count($this->pays);
		}
		parent::init();
	}

	private function getDefaultFirstBillDate(): string {
		$time = null;
		$details = $this->getPayCityDetails();
		if ($details->hasBankTransferDate()) {
			$time = strtotime($details->bank_transfer_at);
		} elseif ($details->hasDirectDate()) {
			$time = strtotime($details->direct_at);
		}
		if ($time !== null) {
			return date($this->dateFormat, strtotime($this->dateInterval, $time));
		}
		return (new DateTime())->modify('last day of')->format($this->dateFormat);
	}

	public function isCreateForm(): bool {
		return $this->getPayCalculation()->isNewRecord;
	}

	public function getPayCalculation(): IssuePayCalculation {
		return $this->calculation;
	}

	private function getDefaultPayType(): int {
		if ($this->getPayCityDetails()->hasBankTransferDate()) {
			return IssuePayCalculation::PAY_TYPE_BANK_TRANSFER;
		}
		return IssuePayCalculation::PAY_TYPE_DIRECT;
	}

	/**
	 * @return IssuePayCity
	 * @todo move to IssuePayCity model.
	 */
	public function getPayCityDetails(): IssuePayCity {
		if ($this->payCity === null) {
			$pay = $this->issue->payCity;
			if ($pay === null) {
				$pay = new IssuePayCity(['city_id' => $this->issue->pay_city_id]);
			}
			$this->payCity = $pay;
		}
		return $this->payCity;
	}

	private function getPaysSumValue(): float {
		$sum = 0;
		foreach ($this->getPays() as $pay) {
			$sum += (float) $pay->value;
		}
		return $sum;
	}

	/**
	 * @return IssuePay[]
	 */
	public function getPays(): array {
		return $this->pays;
	}

	public function load($data, $formName = null): bool {
		if (isset($data[static::GENERATE_NAME])) {
			$this->_isGenerate = true;
		}
		$load = $this->getPayCalculation()->load($data)
			&& parent::load($data);
		if ($load && !$this->isDisallowChangePays()) {
			$this->pays = $this->generatePays();
		}
		if ($this->payParts > 1 && !$this->isGenerate()) {
			$load = $load && Model::loadMultiple($this->pays, $data);
		}
		return $load;
	}

	private function generatePays(): array {
		$pays = [];
		$date = $this->firstBillDate;
		for ($i = 0; $i < $this->payParts; $i++) {
			$pays[] = new IssuePay([
				'issue_id' => $this->issue->id,
				'value' => (int) $this->payParts === 1 ? $this->value : 0,
				'deadline_at' => $date,
				'transfer_type' => $this->getPayCalculation()->pay_type,
				'vat' => $this->issue->type->vat,
				'type' => IssuePay::TYPE_HONORARIUM,
			]);
			$date = date($this->dateFormat, strtotime('+ 1 month', strtotime($date)));
		}
		return $pays;
	}

	public function save(): bool {
		$payCalculation = $this->getPayCalculation();
		$payCalculation->value = $this->value;
		if ($this->validate()) {
			if (empty($this->firstBillDate) || empty($this->payParts)) {
				$payCalculation->status = IssuePayCalculation::STATUS_DRAFT;
			}
			$save = $payCalculation->save(false);
			$status = (int) $this->getPayCalculation()->status;

			foreach ($this->getPays() as $pay) {
				if ($status === IssuePayCalculation::STATUS_DRAFT) {
					$pay->delete();
					continue;
				}
				$pay->vat = $this->issue->type->vat;
				if ($status === IssuePayCalculation::STATUS_PAYED) {
					if (empty($pay->pay_at)) {
						$pay->pay_at = empty($this->payAt) ? date(DATE_ATOM) : $this->payAt;
					}
				}
				if (!$pay->isPayed()) {
					$pay->transfer_type = $payCalculation->pay_type;
				}
				if (!$this->isCreateForm() && (int) $pay->value === 0) {
					$save = $save && $pay->delete();
				} else {
					$save = $save && $pay->save(false);
				}
				if (!$save) {
					break;
				}
			}

			return $save;
		}
		return false;
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
		return $this->getPayCalculation()->validate($attributeNames, $clearErrors)
			&& ($this->isDisallowChangePays() ? Model::validateMultiple($this->getPays(), $attributeNames) : true)
			&& parent::validate($attributeNames, $clearErrors);
	}

	public function isDisallowChangePays(): bool {
		return !$this->isCreateForm() && !$this->getPayCalculation()->isDraft();
	}

	public static function getPaysTypesNames(): array {
		return IssuePayCalculation::getPayTypesNames();
	}

	public static function getStatusNames(): array {
		return IssuePayCalculation::getStatusNames();
	}

}
