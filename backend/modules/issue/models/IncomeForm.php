<?php

namespace backend\modules\issue\models;

use Decimal\Decimal;
use Yii;
use yii\base\Model;

class IncomeForm extends Model {

	public string $costs = '';
	public string $gross = '';
	public string $tax = '';

	public function rules(): array {
		return [
			[['costs', 'gross'], 'number', 'min' => 0],
			[['costs', 'gross'], 'default', 'value' => 0],
			['tax', 'number', 'min' => 0, 'max' => 100],
		];
	}

	public function netIncome(): ?Decimal {
		$income = $this->income();
		if ($income && !$this->hasErrors('tax')) {
			return Yii::$app->tax->netto($income, $this->getDecimal($this->tax));
		}
		return $income;
	}

	public function income(): ?Decimal {
		if ($this->validate()) {
			return $this->getDecimal($this->gross)->sub($this->getDecimal($this->costs));
		}
		return null;
	}

	/**
	 * @param Decimal|string $value
	 * @return Decimal
	 */
	protected function getDecimal($value): Decimal {
		if ($value instanceof Decimal) {
			return $value;
		}
		return new Decimal($value);
	}
}
