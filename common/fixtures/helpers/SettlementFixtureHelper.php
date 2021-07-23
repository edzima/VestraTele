<?php

namespace common\fixtures\helpers;

use common\fixtures\settlement\CalculationCostFixture;
use common\fixtures\settlement\CalculationFixture;
use common\fixtures\settlement\CostFixture;
use common\fixtures\settlement\PayFixture;
use common\fixtures\settlement\PayReceivedFixture;
use common\models\issue\IssueCost;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use Yii;

class SettlementFixtureHelper extends BaseFixtureHelper {

	public const OWNER_JOHN = UserFixtureHelper::MANAGER_JOHN;
	public const OWNER_NICOLE = UserFixtureHelper::MANAGER_NICOLE;

	public const SETTLEMENT = 'settlement.settlement';
	private const COST = 'settlement.cost';
	private const PAY = 'settlement.pay';
	private const OWNER = 'settlement.owner';
	private const PAY_RECEIVED = 'settlement.pay_received';

	protected static function getDefaultDataDirPath(): string {
		return Yii::getAlias('@common/tests/_data/settlement/');
	}

	public function grabCost(int $index): IssueCost {
		return $this->tester->grabFixture(static::COST, $index);
	}

	public function grabSettlement($index): IssuePayCalculation {
		return $this->tester->grabFixture(static::SETTLEMENT, $index);
	}

	public function grabPay($index): IssuePay {
		return $this->tester->grabFixture(static::PAY, $index);
	}

	public function seePay(array $attributes): void {
		$this->tester->seeRecord(IssuePay::class, $attributes);
	}

	public static function settlement(string $baseDataDir = null): array {
		return [
			static::SETTLEMENT => [
				'class' => CalculationFixture::class,
				'dataFile' => static::getDataDirPath($baseDataDir) . 'settlement.php',
			],
		];
	}

	public static function cost(bool $withSettlements, string $baseDataDir = null): array {
		$fixtures = [];
		$fixtures[static::COST] = [
			'class' => CostFixture::class,
			'dataFile' => static::getDataDirPath($baseDataDir) . 'cost.php',
		];
		if ($withSettlements) {
			$fixtures['settlement-cost'] = [
				'class' => CalculationCostFixture::class,
				'dataFile' => static::getDataDirPath($baseDataDir) . 'settlement-cost.php',
			];
		}
		return $fixtures;
	}

	public static function pay(string $baseDataDir = null): array {
		return [
			static::PAY => [
				'class' => PayFixture::class,
				'dataFile' => static::getDataDirPath($baseDataDir) . 'pay.php',
			],
		];
	}

	public static function payReceived(): array {
		return [
			static::PAY_RECEIVED => [
				'class' => PayReceivedFixture::class,
				'dataFile' => static::getDataDirPath() . 'pay-received.php',
			],
		];
	}

	public static function owner(): array {
		return [
			static::OWNER => UserFixtureHelper::manager(),
		];
	}

	public function haveSettlement(string $value, int $type, array $attributes = []): int {
		if (!isset($attributes['owner_id'])) {
			$attributes['owner_id'] = static::OWNER_JOHN;
		}
		$attributes['value'] = $value;
		$attributes['type'] = $type;
		return $this->tester->haveRecord(IssuePayCalculation::class, $attributes);
	}

	public function havePay(string $value, array $attributes = []): int {
		$attributes['value'] = $value;
		if (!isset($attributes['calculation_id'])) {
			$attributes['calculation_id'] = 1;
		}
		if (!isset($attributes['vat'])) {
			$attributes['vat'] = 0;
		}
		return $this->tester->haveRecord(IssuePay::class, $attributes);
	}

}
