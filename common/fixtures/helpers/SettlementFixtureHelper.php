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
use common\models\issue\IssueSettlement;
use Yii;
use yii\helpers\ArrayHelper;

class SettlementFixtureHelper extends BaseFixtureHelper {

	public const DEFAULT_ISSUE_ID = 1;
	public const OWNER_JOHN = UserFixtureHelper::MANAGER_JOHN;
	public const OWNER_NICOLE = UserFixtureHelper::MANAGER_NICOLE;

	public const SETTLEMENT = 'settlement.settlement';
	public const COST = 'settlement.cost';
	public const PAY = 'settlement.pay';
	private const OWNER = 'settlement.owner';
	private const PAY_RECEIVED = 'settlement.pay_received';
	private const DEFAULT_TYPE = IssueSettlement::TYPE_HONORARIUM;

	private ?int $lastSettlementId = null;

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

	public function haveSettlement(string $value, array $attributes = []): int {
		if (!isset($attributes['owner_id'])) {
			$attributes['owner_id'] = static::OWNER_JOHN;
		}
		if (!isset($attributes['issue_id'])) {
			$attributes['issue_id'] = static::DEFAULT_ISSUE_ID;
		}
		if (!isset($attributes['type'])) {
			$attributes['type'] = static::DEFAULT_TYPE;
		}
		$attributes['value'] = $value;
		$this->lastSettlementId = $this->tester->haveRecord(IssuePayCalculation::class, $attributes);
		return $this->lastSettlementId;
	}

	public function findSettlement(int $id): IssueSettlement {
		return IssuePayCalculation::findOne($id);
	}

	public function havePay(string $value, array $attributes = []): int {
		$attributes['value'] = $value;
		if (!isset($attributes['calculation_id'])) {
			if ($this->lastSettlementId === null) {
				$settlementAttributes = ArrayHelper::remove($attributes, 'settlement', []);
				$settlementValue = ArrayHelper::getValue($settlementAttributes, 'value', $value);
				$this->haveSettlement($settlementValue, $settlementAttributes);
			}
			$attributes['calculation_id'] = $this->lastSettlementId;
		}
		if (!isset($attributes['vat'])) {
			$attributes['vat'] = 0;
		}
		return $this->tester->haveRecord(IssuePay::class, $attributes);
	}

	public function findPay(int $id): IssuePay {
		return IssuePay::findOne($id);
	}

}
