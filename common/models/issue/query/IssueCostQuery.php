<?php

namespace common\models\issue\query;

use common\models\issue\IssueCost;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Issue]].
 *
 * @see IssueCost
 */
class IssueCostQuery extends ActiveQuery {

	public function settled(string $dateFrom = null, string $dateTo = null): self {
		if (empty($dateFrom) && empty($dateTo)) {
			$this->andWhere('settled_at IS NOT NULL');
			return $this;
		}
		if (!empty($dateFrom) && !empty($dateTo)) {
			$this->andWhere(['between', 'settled_at', $dateFrom, $dateTo]);
			return $this;
		}
		if (!empty($dateFrom)) {
			$this->andWhere(['>=', 'settled_at', $dateFrom]);
			return $this;
		}
		$this->andWhere(['<=', 'settled_at', $dateTo]);

		return $this;
	}

	public function notSettled(): self {
		$this->andWhere([
			'settled_at' => null,
		]);
		return $this;
	}

	public function withSettlements(): self {
		$this->joinWith('settlements');
		$this->andWhere('settlement_id IS NOT NULL');
		return $this;
	}

	public function withoutSettlements(): self {
		$this->joinWith('settlements');
		$this->andWhere(['settlement_id' => null]);
		return $this;
	}

	public function user(int $id): self {
		$this->andWhere(['user_id' => $id]);
		return $this;
	}

	public function hidden(): self {
		$this->andWhere(['hide_on_report' => true]);
		return $this;
	}

	/**
	 * @inheritdoc
	 * @return IssueCost[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return IssueCost|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

}
