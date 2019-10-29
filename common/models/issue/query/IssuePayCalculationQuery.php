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

	public function notDraft(): self {
		$this->andWhere(['>', 'status' => IssuePayCalculation::STATUS_DRAFT]);
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