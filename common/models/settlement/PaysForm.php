<?php

namespace common\models\settlement;

use common\helpers\DateTimeHelper;
use DateTime;
use Decimal\Decimal;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class PaysForm extends PayForm {

	public const DEADLINE_LAST_DAY_OF_MONTH = 'last day of next month';

	public string $deadlineRange = self::DEADLINE_LAST_DAY_OF_MONTH;
	public int $count = 1;
	public int $minCount = 1;

	/** @var PayInterface[] */
	private array $pays = [];

	private bool $isGenerate = false;

	public function rules(): array {
		return array_merge([
			[['count'], 'required'],
			[
				'deadlineRange', 'required', 'when' => function (): bool {
				return $this->count > 1 && empty($this->deadline_at);
			},
			],
			['count', 'integer', 'min' => $this->minCount],
			[
				'value', 'sumValidate', 'when' => function () { return $this->count > 1; },
			],
		], parent::rules());
	}

	public function sumValidate($attribute): void {
		$value = $this->getValue();
		$sum = Yii::$app->pay->sum($this->getPays());
		if (!$value->equals($sum)) {
			$formatter = Yii::$app->formatter;
			$diff = $sum->sub($value);
			$this->addError($attribute,
				'Suma rat musi być: ' . $formatter->asCurrency($value) . '. 
				Różnica: ' . $formatter->asCurrency($diff) . '.');
		}
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();
		$labels['deadline_at'] = Yii::t('settlement', 'First deadline at');
		$labels['count'] = Yii::t('settlement', 'Count');
		return $labels;
	}

	public function isGenerate(): bool {
		return $this->isGenerate;
	}

	/**
	 * {@inheritDoc}
	 */
	public function load($data, $formName = null): bool {
		$action = ArrayHelper::getValue($data, 'action');
		if ($action === 'generate') {
			$this->isGenerate = true;
		} else {
			$this->isGenerate = false;
		}
		$load = parent::load($data, $formName);
		if ($this->isGenerate || (int) $this->count !== count($this->pays)) {
			$this->pays = $this->generatePays(false);
		}
		if (!$this->isGenerate) {
			$load = $load && Model::loadMultiple($this->getPays(), $data);
		}

		return $load;
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate($attributeNames = null, $clearErrors = true): bool {
		$validate = parent::validate($attributeNames, $clearErrors);
		$validate = $validate && Model::validateMultiple($this->pays, $attributeNames);
		return $validate;
	}

	public function hasManyPays(): bool {
		return $this->count > 1;
	}

	/**
	 * @param PayInterface[]
	 */
	public function setPays(array $pays): void {
		$this->pays = $pays;
	}

	/**
	 * @return PayInterface[]
	 */
	public function getPays(): array {
		return $this->pays;
	}

	/**
	 * @return PayInterface[]
	 */
	public function generatePays(bool $validate = true): array {
		if ($validate && !$this->validate()) {
			return [];
		}
		$value = $this->getValue();
		if ($value === null || $value->isNegative()) {
			return [];
		}
		$deadline = $this->getDeadlineAt();
		$pays = [];
		for ($i = 0; $i < $this->count; $i++) {
			$pay = $this->generatePay(false, $value->div($this->count), $deadline);
			$pays[] = $pay;
			if ($deadline !== null && $this->deadlineRange === static::DEADLINE_LAST_DAY_OF_MONTH) {
				$deadline = DateTimeHelper::lastDayOfMonth(
					DateTimeHelper::addMonth($deadline)
				);
			}
		}
		return $pays;
	}

	public function generatePay(bool $validate = true, ?Decimal $value = null, ?DateTime $deadline = null): ?PayInterface {
		if ($validate && !$this->validate()) {
			return null;
		}
		if ($value === null) {
			$value = $this->getValue();
		}
		if ($deadline === null) {
			$deadline = $this->getDeadlineAt();
		}
		return new PayForm([
			'value' => $value->toFixed(2),
			'vat' => $this->getVAT() ? $this->getVAT()->toFixed(2) : null,
			'transferType' => $this->getTransferType(),
			'payment_at' => $this->getPaymentAt() ? $this->getPaymentAt()->format($this->dateFormat) : null,
			'deadline_at' => $deadline ? $deadline->format($this->dateFormat) : null,
		]);
	}

}
