<?php

namespace common\models\issue\query;

use common\models\issue\IssuePayCalculation;
use yii\db\ActiveQuery;

/**
 * Class IssuePayCalculationQuery
 *
 * @see IssuePayCalculation
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssuePayCalculationQuery extends ActiveQuery {

	public function withoutProvisions(): self {
		$this->joinWith('pays.provisions');
		$this->andWhere(['provision.pay_id' => null]);
		$this->groupBy('id');
		return $this;
	}

	/**
	 * @inheritdoc
	 * @return IssuePayCalculation[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return IssuePayCalculation|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}
}
