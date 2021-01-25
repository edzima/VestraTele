<?php

namespace common\models\user;

use common\models\user\query\UserQuery;

class Customer extends User {

	public const ROLE_DEFAULT = self::ROLE_CUSTOMER;

	//@todo change client to customer before move to repo
	public const ROLE_CUSTOMER = 'client';
	public const ROLE_VICTIM = 'victim';
	public const ROLE_SHAREHOLDER = 'shareholder';
	public const ROLE_HANDICAPPED = 'handicapped';

	public const ROLES = [
		self::ROLE_CUSTOMER,
		self::ROLE_VICTIM,
		self::ROLE_SHAREHOLDER,
		self::ROLE_HANDICAPPED,
	];

	/**
	 * @inheritdoc
	 */
	public static function find(): UserQuery {
		return parent::find()->customers();
	}

	public static function fromUser(User $user): self {
		return new self($user->attributes);
	}
}
