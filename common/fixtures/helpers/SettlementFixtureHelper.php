<?php

namespace common\fixtures\helpers;

use common\fixtures\settlement\CalculationCostFixture;
use common\fixtures\settlement\CalculationFixture;
use common\fixtures\settlement\CostFixture;
use common\fixtures\settlement\CostTypeFixture;
use common\fixtures\settlement\PayFixture;
use common\fixtures\settlement\PayReceivedFixture;
use common\fixtures\settlement\SettlementTypeFixture;
use common\models\issue\IssueCost;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSettlement;
use common\models\settlement\SettlementType;
use Yii;
use yii\helpers\ArrayHelper;

class SettlementFixtureHelper extends BaseFixtureHelper {

	public const DEFAULT_ISSUE_ID = 1;
	public const OWNER_JOHN = UserFixtureHelper::MANAGER_JOHN;
	public const OWNER_NICOLE = UserFixtureHelper::MANAGER_NICOLE;

	public const DEFAULT_PROVIDER_ID = 1;

	public const SETTLEMENT = 'settlement.settlement';

	public const TYPE = 'settlement.type';

	public const COST = 'settlement.cost';

	public const COST_TYPE = 'settlement.cost.type';
	public const PAY = 'settlement.pay';
	private const OWNER = 'settlement.owner';
	private const PAY_RECEIVED = 'settlement.pay_received';
	private const DEFAULT_TYPE = self::TYPE_ID_HONORARIUM;
	const DEFAULT_PROVIDER_TYPE = IssueSettlement::PROVIDER_CLIENT;

	public const TYPE_ID_ADMINISTRATIVE = 10;
	public const TYPE_ID_HONORARIUM = 30;
	public const TYPE_ID_LAWYER = 40;
	public const TYPE_ID_NOT_ACTIVE = 50;

	public const TYPE_ID_PERCENTAGE = 100;
	public const COST_TYPE_ID_OFFICE = 20;

	public const COST_TYPE_PURCHASE_OF_RECEIVABLES = 30;

	public const COST_TYPE_JUSTIFICATION_OF_THE_JUDGMENT = 40;

	public const COST_TYPE_ID_INSTALLMENT = 50;

	public const COST_TYPE_ID_NOT_ACTIVE = -1;

	private ?int $lastSettlementId = null;

	public static function getTypeManagerPermission(): string {
		return SettlementType::instance()->getModelAccess()->managerPermission;
	}

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

	public static function type(string $baseDataDir = null): array {
		return [
			static::TYPE => [
				'class' => SettlementTypeFixture::class,
				'dataFile' => static::getDataDirPath($baseDataDir) . 'type.php',
			],
		];
	}

	public static function costType(): array {
		return [
			static::COST_TYPE => [
				'class' => CostTypeFixture::class,
				'dataFile' => static::getDataDirPath() . 'cost-type.php',
			],
		];
	}

	public static function cost(bool $withSettlements, string $baseDataDir = null): array {
		$fixtures = static::costType();
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
		if (!isset($attributes['type_id'])) {
			$attributes['type_id'] = static::DEFAULT_TYPE;
		}
		if (!isset($attributes['provider_id'])) {
			$attributes['provider_id'] = static::DEFAULT_PROVIDER_ID;
		}
		if (!isset($attributes['provider_type'])) {
			$attributes['provider_type'] = static::DEFAULT_PROVIDER_TYPE;
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
		return $this->tester->haveRecord(IssuePay::class, $attributes);
	}

	public function findPay(int $id): IssuePay {
		return IssuePay::findOne($id);
	}

}
