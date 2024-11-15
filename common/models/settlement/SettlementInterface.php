<?php

namespace common\models\settlement;

use Decimal\Decimal;
use yii\db\ActiveQuery;

/**
 * Interface SettlementInterface
 *
 * @property-read PayInterface[] $pays
 * @property-read CostInterface[] $costs
 * @property-read SettlementType $type
 */
interface SettlementInterface extends PayedInterface {

	public function getTypeId(): int;

	public function getTypeName(): string;

	public static function getTypesNames(bool $active = true): array;

	public function getValue(): Decimal;

	public function getValueToPay(): Decimal;

	public function getPays(): ActiveQuery;

	public function getCosts(): ActiveQuery;

	public function unlinkCosts(): void;

	public function linkCosts(array $costs_ids): int;
}
