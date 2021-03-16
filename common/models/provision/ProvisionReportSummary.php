<?php

namespace common\models\provision;

use common\models\issue\IssueCost;
use Decimal\Decimal;
use yii\base\BaseObject;

class ProvisionReportSummary extends BaseObject {

	/**
	 * @var Provision[]
	 */
	public array $provisions = [];

	/**
	 * @var IssueCost[]
	 */
	public array $settledCosts = [];

	/**
	 * @var IssueCost[]
	 */
	public array $notSettledCosts = [];

	public function getTotalSum(): Decimal {
		return $this->getProvisionsSum()
			->add($this->getNotSettledCostsSum())
			->sub($this->getSettledCostsSum());
	}

	public function getProvisionsSum(): Decimal {
		$sum = new Decimal(0);
		foreach ($this->provisions as $provision) {
			$sum = $sum->add($provision->getValue());
		}
		return $sum;
	}

	public function getSettledCostsSum(): Decimal {
		$sum = new Decimal(0);
		foreach ($this->settledCosts as $cost) {
			$sum = $sum->add($cost->getValueWithoutVAT());
		}
		return $sum;
	}

	public function getNotSettledCostsSum(): Decimal {
		$sum = new Decimal(0);
		foreach ($this->notSettledCosts as $cost) {
			$sum = $sum->add($cost->getValueWithoutVAT());
		}
		return $sum;
	}

}
