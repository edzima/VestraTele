<?php

namespace common\modules\lead\models\query;

use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\LeadDialerType;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for LeadDialer.
 *
 * @see LeadDialer
 */
class LeadDialerQuery extends ActiveQuery {

	/**
	 * @inheritdoc
	 * @return LeadDialer[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return LeadDialer|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	public function activeType(): self {
		$this->joinWith('type');
		$this->andWhere([LeadDialerType::tableName() . '.status' => LeadDialerType::STATUS_ACTIVE]);
		return $this;
	}
}
