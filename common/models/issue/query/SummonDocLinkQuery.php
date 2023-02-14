<?php

namespace common\models\issue\query;

use common\models\issue\SummonDocLink;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[SummonDocLink]].
 *
 * @see SummonDocLink
 */
class SummonDocLinkQuery extends ActiveQuery {

	/**
	 * @inheritdoc
	 * @return SummonDocLink[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return SummonDocLink|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	public function status(string $status) {
		switch ($status) {
			case SummonDocLink::STATUS_TO_DO:
				$this->andWhere(['done_at' => null]);
				$this->andWhere(['confirmed_at' => null]);
				break;
			case SummonDocLink::STATUS_TO_CONFIRM:
				$this->andWhere(['NOT', ['done_at' => null]]);
				$this->andWhere(['confirmed_at' => null]);
				break;
			case SummonDocLink::STATUS_CONFIRMED:
				$this->andWhere(['NOT', ['done_at' => null]]);
				$this->andWhere(['NOT', ['confirmed_at' => null]]);
				break;
		}
	}
}
