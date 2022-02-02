<?php

namespace common\models\settlement;

use Decimal\Decimal;
use yii\db\ActiveQuery;

/**
 * Interface SettlementInterface
 *
 * @property-read PayInterface[] $pays
 * @property-read CostInterface[] $costs
 */
interface SettlementInterface extends PayedInterface {

	public function getType(): int;

	public function getTypeName(): string;

	public static function getTypesNames(): array;

	public function getValue(): Decimal;

	public function getValueToPay(): Decimal;

	public function getPays(): ActiveQuery;

	public function getCosts(): ActiveQuery;

	public function unlinkCosts(): void;

	public function linkCosts(array $costs_ids): int;
}
