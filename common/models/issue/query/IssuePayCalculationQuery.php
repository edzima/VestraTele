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

	public function onlyTypes(array $types): self {
		[$table, $alias] = $this->getTableNameAndAlias();
		$this->andWhere([$alias . '.type_id' => $types]);
		return $this;
	}

	public function withoutProvisions(): self {
		$this->joinWith('pays.provisions');
		$this->andWhere(['provision.pay_id' => null]);
		$this->groupBy('id');
		return $this;
	}

	public function onlyWithoutProblems(): self {
		$this->andWhere('problem_status is NULL');
		return $this;
	}

	public function onlyProblems(array $problems = []): self {
		if (empty($problems)) {
			$this->andWhere('problem_status IS NOT NULL');
		} else {
			$this->andWhere(['problem_status' => $problems]);
		}
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
