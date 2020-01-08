<?php

namespace common\models\provision;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[ProvisionType]].
 *
 * @see ProvisionType
 */
class ProvisionTypeQuery extends ActiveQuery {

	/**
	 * {@inheritdoc}
	 * @return ProvisionType[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * {@inheritdoc}
	 * @return ProvisionType|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}
}
