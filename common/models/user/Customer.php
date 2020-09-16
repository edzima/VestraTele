<?php

namespace common\models\user;

use common\models\user\query\UserQuery;

class Customer extends User {

	/**
	 * @inheritdoc
	 */
	public static function find(): UserQuery {
		return parent::find()->customers();
	}
}
