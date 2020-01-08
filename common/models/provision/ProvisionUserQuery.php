<?php

namespace common\models\provision;

use common\models\User;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[ProvisionUser]].
 *
 * @see ProvisionUser
 */
class ProvisionUserQuery extends ActiveQuery {

	public function user(User $user): self {
		return $this->andWhere([
			'or', [
				'from_user_id' => $user->id,
				'to_user_id' => $user->getParentsIds(),
			],
			[
				'from_user_id' => $user->getAllChildesIds(),
				'to_user_id' => $user->id,
			],
			[
				'from_user_id' => $user->id,
				'to_user_id' => $user->id,
			],

		]);
	}

	/**
	 * {@inheritdoc}
	 * @return ProvisionUser[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * {@inheritdoc}
	 * @return ProvisionUser|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}
}
