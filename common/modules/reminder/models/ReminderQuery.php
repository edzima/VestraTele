<?php

namespace common\modules\reminder\models;

use yii\db\ActiveQuery;
use yii\db\Expression;

class ReminderQuery extends ActiveQuery {

	public function onlyUser(int $userId, bool $withNotSet = true): self {
		[$table, $alias] = $this->getTableNameAndAlias();
		$this->andWhere([$alias . '.user_id' => $userId]);
		if ($withNotSet) {
			$this->orWhere([$alias . '.user_id' => null]);
		}
		return $this;
	}

	public function onlyDelayed(): self {
		[$table, $alias] = $this->getTableNameAndAlias();
		$this->andWhere(['>', new Expression("DATEDIFF(CURDATE(), $alias.date_at)"), 0]);
		return $this;
	}

	public function onlyToday(): self {
		[$table, $alias] = $this->getTableNameAndAlias();
		$this->andWhere(['=', new Expression("DATEDIFF(CURDATE(), $alias.date_at)"), 0]);
		return $this;
	}

	public function orderByDateAndPriority(): self {
		[$table, $alias] = $this->getTableNameAndAlias();

		$this->orderBy([
			"$alias.priority" => SORT_DESC,
			"$alias.date_at" => SORT_ASC,
		]);
		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @return Reminder|null|array
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	/**
	 * {@inheritDoc}
	 * @return Reminder[]
	 */
	public function all($db = null) {
		return parent::all($db);
	}

}
