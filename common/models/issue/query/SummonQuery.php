<?php

namespace common\models\issue\query;

use common\models\issue\Summon;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Summon]].
 *
 * @see Summon
 */
class SummonQuery extends ActiveQuery {

	public function user(int $userId): self {
		$this->users([$userId]);
		return $this;
	}

	public function users(array $usersIds): self {
		[, $alias] = $this->getTableNameAndAlias();

		$this->andWhere([
			'or', [
				$alias . '.owner_id' => $usersIds,
			], [
				$alias . '.contractor_id' => $usersIds,
			],
		]);
		return $this;
	}

	public function imminentDeadline(string $range = '+1 day'): self {
		$date = date('Y-m-d', strtotime($range));
		$this->andWhere(['<=', Summon::tableName() . '.deadline_at', $date]);
		return $this;
	}

	public function active(): self {
		[$table, $alias] = $this->getTableNameAndAlias();
		$this->andWhere(['NOT IN', $alias . '.status', Summon::notActiveStatuses()]);
		return $this;
	}

	/**
	 * @inheritdoc
	 * @return Summon[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return Summon|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

}
