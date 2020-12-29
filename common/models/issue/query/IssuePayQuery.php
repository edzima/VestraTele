<?php

namespace common\models\issue\query;

use common\models\issue\IssuePay;
use Decimal\Decimal;
use yii\db\ActiveQuery;
use yii\db\Expression;

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

	public function onlyNotPayed(): self {
		[, $alias] = $this->getTableNameAndAlias();
		$this->andWhere("$alias.pay_at IS NULL");
		return $this;
	}

	public function onlyDelayed(): self {
		[, $alias] = $this->getTableNameAndAlias();
		$this->onlyNotPayed();
		$this->andWhere(['<=', $alias . '.deadline_at', date('Y-m-d')]);
		return $this;
	}

	public function onlyMaxDelayed(int $days): self {
		[, $alias] = $this->getTableNameAndAlias();
		$this->andWhere(['<=', new Expression("DATEDIFF(CURDATE(), $alias.deadline_at)"), $days]);

		return $this;
	}

	public function onlyMinDelayed(int $days): self {
		[, $alias] = $this->getTableNameAndAlias();
		$this->andWhere(['>=', new Expression("DATEDIFF(CURDATE(), $alias.deadline_at)"), $days]);
		return $this;
	}

	public function onlyNotDelayed(): self {
		[, $alias] = $this->getTableNameAndAlias();
		$this->andWhere(['>=', $alias . '.deadline_at', date('Y-m-d')]);
		return $this;
	}

	public function onlyPayed(): self {
		[, $alias] = $this->getTableNameAndAlias();
		$this->andWhere($alias . '.pay_at IS NOT NULL');
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

	public function onlyWithoutDeadline(): self {
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
