<?php

namespace common\models\provision;

use common\models\issue\IssueCost;
use Decimal\Decimal;
use Yii;
use yii\base\Model;

class ProvisionReportSummary extends Model {

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

	public function attributeLabels() {
		return [
			'settledCostsSum' => Yii::t('provision', 'Settled costs sum'),
			'notSettledCostsSum' => Yii::t('provision', 'Not settled costs sum'),
			'provisionsSum' => Yii::t('provision', 'Provisions sum'),
			'totalSum' => Yii::t('provision', 'Total sum'),
		];
	}

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
