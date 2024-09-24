<?php

namespace common\models\settlement\query;

use common\models\settlement\SettlementType;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[SettlementType]].
 *
 * @see SettlementType
 */
class SettlementTypeQuery extends ActiveQuery {

	/**
	 * @inheritdoc
	 * @return SettlementType[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return SettlementType|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	public function active(): self {
		$this->andWhere(['is_active' => true]);
		return $this;
	}

	public function forIssueTypes(array $ids): self {
		if (!empty($ids)) {
			$this->joinWith(['issueTypes IT']);
			$this->andWhere(['or', ['IT.id' => $ids], ['IT.id' => null]]);
		}
		return $this;
	}

}
