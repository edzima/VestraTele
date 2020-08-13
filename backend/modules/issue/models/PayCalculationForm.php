<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class PayCalculationForm extends Model {

	public const GENERATE_NAME = 'generate';
	public const RANGE_MONTH = '+ 1 month';
	public const RANGE_2_WEEKS = '+ 2 weeks';

	public $type;
	public $value;
	public $vat;
	public $payTransferType = IssuePay::TRANSFER_TYPE_BANK;
	public $paysCount = 1;
	public $paysRange = self::RANGE_MONTH;
	public $providerType;

	public $paymentAt;
	public $deadlineAt;
	public $dateFormat = 'Y-m-d';

	public $details;

	/** @var Issue */
	private $issue;
	/** @var IssuePay[] */
	private $pays = [];
	private $isGenerate = false;

	/** @var IssuePayCalculation */
	private $model;

	public function rules(): array {
		return [
			[['value', 'paysCount', 'vat', 'type', 'payTransferType', 'providerType'], 'required'],
			['vat', 'number', 'min' => 0, 'max' => 100],
			['value', 'number', 'min' => 1],
			['paysCount', 'integer', 'min' => $this->getMinPaysCount()],

			['value', 'number', 'numberPattern' => '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/', 'enableClientValidation' => false],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			['payTransferType', 'in', 'range' => array_keys(static::getPaysTransferTypesNames())],
			['providerType', 'in', 'range' => array_keys($this->getProvidersNames())],
			[['deadlineAt', 'paymentAt'], 'date', 'format' => DATE_ATOM],
			[
				'value', 'paysSumValidate', 'when' => function () { return $this->paysCount > 1; },
			],

		];
	}

	public function attributeLabels(): array {
		return [
			'details' => 'Uwagi',
			'deadlineAt' => 'Termin płatności',
			'paysCount' => 'Ilość rat',
			'payTransferType' => 'Płatność',
			'value' => 'Wartość (Brutto)',
			'type' => 'Typ',
			'paymentAt' => 'Data płatności',
			'providerType' => 'Wpłacający',

		];
	}

	public function getMinPaysCount(): int {
		$payedCount = count(IssuePay::payedFilter($this->getPays()));
		if ($payedCount > 0) {
			return $payedCount;
		}
		return 1;
	}

	public function paysSumValidate($attribute, $params, $validator): void {
		$value = (float) $this->$attribute;
		$sum = $this->getPaysSumValue();
		if ($value !== $sum) {
			$formatter = Yii::$app->formatter;
			$diff = $sum - $value;
			$this->addError($attribute,
				'Suma rat musi być: ' . $formatter->asDecimal($value) . '. 
				Różnica: ' . $formatter->asDecimal($diff) . '.');
		}
	}

	public function setModel(IssuePayCalculation $model): void {
		$this->model = $model;
		$this->setIssue($model->issue);
		$this->setPays($model->pays);
		$this->value = $this->getPaysSumValue();
		$this->paymentAt = $model->payment_at;
		if (!$this->hasManyPays()) {
			$pays = $this->getPays();
			$pay = reset($pays);
			if ($pay) {
				$this->deadlineAt = $pay->deadline_at;
				$this->paymentAt = $pay->pay_at;
				$this->vat = $pay->vat;
				$this->value = $pay->value;
				$this->payTransferType = $pay->transfer_type;
			}
		}
	}

	public function getId(): int {
		return $this->getModel()->id;
	}

	public function getModel(): IssuePayCalculation {
		if ($this->model === null) {
			$this->model = $this->createModel();
		}
		return $this->model;
	}

	public function createModel(): IssuePayCalculation {
		$model = new IssuePayCalculation();
		$model->issue_id = $this->getIssue()->id;
		return $model;
	}

	public function getIssue(): Issue {
		return $this->issue;
	}

	public function setIssue(Issue $model): void {
		$this->issue = $model;
		$this->value = $model->getProvision()->getSum();
		$this->vat = $model->type->vat;
	}

	/**
	 * @param IssuePay[] $pays
	 */
	private function setPays(array $pays): void {
		$this->pays = ArrayHelper::index($pays, 'id');
		$this->paysCount = count($this->pays);
	}

	/**
	 * @return IssuePay[]
	 */
	public function getPays(): array {


		if ((int) $this->paysCount !== count($this->pays) || $this->isGenerate) {
			$this->pays = $this->generatePays(IssuePay::payedFilter($this->pays));
		}

		return $this->pays;
	}

	/**
	 * @param IssuePay[] $pays already payed pays.
	 * @return IssuePay[]
	 * @throws InvalidConfigException
	 */
	protected function generatePays(array $pays = []): array {
		if ($this->paysCount < 1) {
			throw new InvalidConfigException('$paysCount mu be greater than 0.');
		}
		$date = $this->deadlineAt;
		$paysCount = $this->paysCount - count($pays);
		for ($i = 0; $i < $paysCount; $i++) {
			$pays[] = new IssuePay([
				'vat' => $this->vat,
				'value' => $this->value / $paysCount,
				'deadline_at' => $date,
				'transfer_type' => $this->payTransferType,
			]);
			$date = date($this->dateFormat, strtotime($this->paysRange, strtotime($date)));
		}
		return $pays;
	}

	public static function paysRange(): array {
		return [
			static::RANGE_MONTH => 'Miesiąc',
			static::RANGE_2_WEEKS => '2 tygodnie',
		];
	}

	public function init(): void {
		$this->deadlineAt = $this->deadlineAt ?? $this->getDefaultDeadlineAt();
		parent::init();
	}

	public function setValue($value): void {
		$this->value = str_replace(',', '.', $value);
	}

	public function isGenerate(): bool {
		return $this->isGenerate;
	}

	public function isCreateForm(): bool {
		return $this->getModel()->isNewRecord;
	}

	public function isPayed(): bool {
		return $this->getModel()->isPayed();
	}

	private function getPaysSumValue(): float {
		$sum = 0;
		foreach ($this->getPays() as $pay) {
			$sum += (float) $pay->value;
		}
		return $sum;
	}

	public function load($data, $formName = null): bool {
		$load = parent::load($data, $formName);
		if (isset($data[static::GENERATE_NAME])) {
			$this->isGenerate = true;
			$this->pays = $this->generatePays(IssuePay::payedFilter($this->pays));
		}

		if ($this->hasManyPays()) {
			$load = $load && Model::loadMultiple($this->getPays(), $data);
		}

		return $load;
	}

	public function save(): bool {
		if ($this->validate()) {
			$model = $this->getModel();
			$model->value = $this->value;
			$model->type = $this->type;
			$model->payment_at = $this->paymentAt;
			$model->provider_type = $this->providerType;
			$isNewRecord = $this->isCreateForm();
			$save = $model->save(false);

			if (!$isNewRecord) {
				foreach ($model->pays as $oldPay) {
					$exist = false;
					foreach ($this->getPays() as $pay) {
						if ((int) $oldPay->id === (int) $pay->id) {
							$exist = true;
							break;
						}
					}
					if (!$exist) {
						$oldPay->delete();
					}
				}
			}
			$i = 0;
			foreach ($this->getPays() as $pay) {
				$i++;
				$pay->calculation_id = $model->id;

				if (!$this->hasManyPays() || $this->isGenerate()) {
					$pay->transfer_type = $this->payTransferType;
					$pay->vat = $this->vat;
					$pay->value = $this->value / $i;
				}
				if (!$this->hasManyPays()) {
					$pay->deadline_at = $this->deadlineAt;
					$pay->pay_at = $this->paymentAt;
				}
				if (!$pay->isPayed()) {
					$pay->pay_at = $this->paymentAt;
				}

				if ((int) $pay->value === 0) {
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
		$validate = parent::validate($attributeNames, $clearErrors);
		if ($this->hasManyPays()) {
			$validate = $validate && Model::validateMultiple($this->getPays());
		}
		return $validate;
	}

	public function hasManyPays(): bool {
		return $this->paysCount > 1;
	}

	protected function getDefaultDeadlineAt(): string {
		return (new DateTime())->modify('last day of')->format($this->dateFormat);
	}

	public function getProvidersNames(): array {
		return [
			IssuePayCalculation::PROVIDER_CLIENT => 'Klient - ' . $this->getModel()->issue->getClientFullName(),
			IssuePayCalculation::PROVIDER_RESPONSIBLE_ENTITY => 'Podmiot - ' . $this->getModel()->issue->entityResponsible->name,
		];
	}

	public static function getTypesNames(): array {
		return IssuePayCalculation::getTypesNames();
	}

	public static function getPaysTransferTypesNames(): array {
		return IssuePay::getTransferTypesNames();
	}

}
