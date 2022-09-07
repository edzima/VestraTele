<?php

namespace common\modules\reminder\models;

use yii\db\ActiveQuery;
use yii\db\Expression;

class ReminderQuery extends ActiveQuery {

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

	/**
	 * {@inheritDoc}
	 * @return Reminder|null
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
