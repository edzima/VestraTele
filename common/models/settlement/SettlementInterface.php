<?php

namespace common\models\settlement;

use yii\db\ActiveQuery;

/**
 * Interface SettlementInterface
 *
 * @property-read PayInterface[] $pays
 */
interface SettlementInterface {

	public function getType(): int;

	public function getTypeName(): string;

	public static function getTypesNames(): array;

	/**
	 * @return PayInterface[]
	 */
	public function getPays(): ActiveQuery;
}
