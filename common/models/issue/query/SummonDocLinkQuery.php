<?php

namespace common\models\issue\query;

use common\models\issue\SummonDocLink;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[SummonDocLink]].
 *
 * @see SummonDocLinkQuery
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
}
