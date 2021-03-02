<?php

namespace common\models\issue\query;

use common\models\issue\IssueCost;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Issue]].
 *
 * @see IssueCost
 */
class IssueCostQuery extends ActiveQuery {

	public function withSettlements(): self {
		$this->joinWith('settlements');
		$this->andWhere('settlement_id IS NOT NULL');
		return $this;
	}

	public function withoutSettlements(): self {
		$this->joinWith('settlements');
		$this->andWhere(['settlement_id' => null]);
		return $this;
	}

	/**
	 * @inheritdoc
	 * @return IssueCost[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return IssueCost|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

}
