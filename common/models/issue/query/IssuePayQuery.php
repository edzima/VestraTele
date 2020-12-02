<?php

namespace common\models\issue\query;

use common\models\issue\IssuePay;
use Decimal\Decimal;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[IssueNote]].
 *
 * @see IssuePay
 */
class IssuePayQuery extends ActiveQuery {

	private $ids;

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
		[, $alias] = $this->getTableNameAndAlias();
		$this->andWhere("$alias.pay_at IS NULL or $alias.pay_at = 0");
		return $this;
	}

	public function onlyDelayed(string $delayRange = 'now') {
		[, $alias] = $this->getTableNameAndAlias();
		$this->onlyNotPayed();
		$this->andWhere(['<=', $alias . '.deadline_at', date(DATE_ATOM, strtotime($delayRange))]);
		return $this;
	}

	public function onlyNotDelayed(string $delayRange = 'now') {
		[, $alias] = $this->getTableNameAndAlias();
		$this->andWhere(['>=', $alias . '.deadline_at', date(DATE_ATOM, strtotime($delayRange))]);
		return $this;
	}

	public function onlyPayed() {
		[, $alias] = $this->getTableNameAndAlias();
		$this->andWhere($alias . '.pay_at > 0');
		return $this;
	}

	public function getValueSum(): Decimal {
		[, $alias] = $this->getTableNameAndAlias();
		$sum = $this->sum($alias . '.value');
		if ($sum === null) {
			$sum = 0;
		}
		return new Decimal($sum);
	}

	public function getPayedSum(): Decimal {
		$query = clone $this;
		return $query->onlyPayed()->getValueSum();
	}

	public function onlyWithoutDeadline() {
		[, $alias] = $this->getTableNameAndAlias();
		$this->andWhere($alias . '.deadline_at IS NOT NULL');
		return $this;
	}

	public function getIds(bool $refresh = false): array {
		if ($refresh || $this->ids === null) {
			$model = clone($this);
			$model->select('id');
			$this->ids = $model->column();
		}
		return $this->ids;
	}
}
