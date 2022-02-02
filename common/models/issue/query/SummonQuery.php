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
		[, $alias] = $this->getTableNameAndAlias();

		$this->andWhere([
			'or', [
				$alias . '.owner_id' => $userId,
			], [
				$alias . '.contractor_id' => $userId,
			],
		]);
		return $this;
	}

	public function active(): self {
		[, $alias] = $this->getTableNameAndAlias();
		$this->andWhere(['!=', $alias . '.status', [Summon::STATUS_REALIZED, Summon::STATUS_UNREALIZED]]);
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
