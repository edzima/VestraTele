<?php

namespace common\models\query;

use common\models\PotentialClient;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\PotentialClient]].
 *
 * @see PotentialClient
 */
class PotentialClientQuery extends ActiveQuery implements PhonableQuery {

	use PhonableQueryTrait;

	/**
	 * @inheritdoc
	 * @return PotentialClient[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return PotentialClient|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}
}
