<?php

namespace common\models\provision;

use common\models\settlement\VATInfo;
use common\models\user\User;
use Decimal\Decimal;
use Yii;
use yii\base\Model;
use yii\data\DataProviderInterface;

class ProvisionReportSummary extends Model {

	public DataProviderInterface $provisionsDataProvider;
	public DataProviderInterface $settledCostsDataProvider;
	public DataProviderInterface $notSettledCostsDataProvider;

	public function __construct(User $user, $config = []) { parent::__construct($config); }

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
		foreach ($this->getProvisions() as $provision) {
			$sum = $sum->add($provision->getValue());
		}
		return $sum;
	}

	/**
	 * @return Provision[]
	 */
	public function getProvisions(): array {
		return $this->provisionsDataProvider->getModels();
	}

	/**
	 * @return VATInfo[]
	 */
	public function getNotSettledCosts(): array {
		return $this->notSettledCostsDataProvider->getModels();
	}

	/**
	 * @return VATInfo[]
	 */
	public function getSettledCosts(): array {
		return $this->settledCostsDataProvider->getModels();
	}

	public function getSettledCostsSum(): Decimal {
		$sum = new Decimal(0);
		foreach ($this->getSettledCosts() as $cost) {
			$sum = $sum->add($cost->getValueWithoutVAT());
		}
		return $sum;
	}

	public function getNotSettledCostsSum(): Decimal {
		$sum = new Decimal(0);
		foreach ($this->getNotSettledCosts() as $cost) {
			$sum = $sum->add($cost->getValueWithoutVAT());
		}
		return $sum;
	}

}
