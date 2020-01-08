<?php

namespace common\models\issue;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[IssueNote]].
 *
 * @see IssuePay
 */
class IssuePayQuery extends ActiveQuery {

	/**
	 * @inheritdoc
	 * @return IssuePay[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return IssuePay|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	public function onlyNotPayed() {
		list(, $alias) = $this->getTableNameAndAlias();
		$this->andWhere("$alias.pay_at IS NULL or $alias.pay_at = 0");
		return $this;
	}

	public function onlyDelayed(string $delayRange = 'now') {
		list(, $alias) = $this->getTableNameAndAlias();
		$this->onlyNotPayed();
		$this->andWhere(['<=', $alias . '.deadline_at', date(DATE_ATOM, strtotime($delayRange))]);
		return $this;
	}

	public function onlyNotDelayed(string $delayRange = 'now') {
		list(, $alias) = $this->getTableNameAndAlias();
		$this->andWhere(['>=', $alias . '.deadline_at', date(DATE_ATOM, strtotime($delayRange))]);
		return $this;
	}

	public function onlyPayed() {
		list(, $alias) = $this->getTableNameAndAlias();
		$this->andWhere($alias . '.pay_at > 0');
		return $this;
	}

	public function getValueSum(): float {
		list(, $alias) = $this->getTableNameAndAlias();

		return $this->sum($alias . '.value') ?? 0;
	}

	public function getPayedSum(): float {
		$query = clone $this;
		return $query->onlyPayed()->getValueSum();
	}

	public function onlyWithoutDeadline() {
		list(, $alias) = $this->getTableNameAndAlias();
		$this->andWhere($alias . '.deadline_at IS NOT NULL');
		return $this;
	}
}
