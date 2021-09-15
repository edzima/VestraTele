<?php

namespace common\models\user\query;

use common\models\query\PhonableQuery;
use common\models\query\PhonableQueryTrait;
use common\models\user\UserProfile;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\user\UserProfile]].
 *
 * @see UserProfile
 */
class UserProfileQuery extends ActiveQuery implements PhonableQuery {

	use PhonableQueryTrait;

	protected function getPhoneColumns(): array {
		return [
			'phone',
			'phone_2',
		];
	}

	/**
	 * @inheritdoc
	 * @return UserProfile[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return UserProfile|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

}
