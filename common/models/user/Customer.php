<?php

namespace common\models\user;

use common\models\user\query\UserQuery;
use Yii;

class Customer extends User {

	public const ROLE_DEFAULT = self::ROLE_CUSTOMER;

	//@todo change client to customer before move to repo
	public const ROLE_CUSTOMER = 'client';
	public const ROLE_VICTIM = 'victim';
	public const ROLE_SHAREHOLDER = 'shareholder';

	public const ROLES = [
		self::ROLE_CUSTOMER,
		self::ROLE_VICTIM,
		self::ROLE_SHAREHOLDER,
	];

	/**
	 * @inheritdoc
	 */
	public static function find(): UserQuery {
		return parent::find()->customers();
	}

	public static function deleteAll($condition = null, $params = []) {
		//@todo add test, with condition.
		$count = 0;
		foreach (static::ROLES as $role) {
			$ids = Yii::$app->authManager->getUserIdsByRole($role);
			if ($condition === null) {
				$count += parent::deleteAll(['id' => $ids]);
			} else {
				$extendedCondition = [$condition];
				$extendedCondition[] = ['id' => $ids];
				$count += parent::deleteAll($extendedCondition, $params);
			}
		}
		return $count;
	}

	public static function fromUser(User $user): self {
		return new self($user->attributes);
	}
}
