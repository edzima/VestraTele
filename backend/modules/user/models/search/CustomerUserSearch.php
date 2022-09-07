<?php

namespace backend\modules\user\models\search;

use common\models\user\Customer;
use common\models\user\query\UserQuery;

class CustomerUserSearch extends UserSearch {

	public array $defaultOrder = [
		'updated_at' => SORT_DESC,
	];

	protected function createQuery(): UserQuery {
		return Customer::find()
			->joinWith('addresses.address.city');
	}

	public static function getStatusesNames(): array {
		$names = parent::getStatusesNames();
		unset($names[static::STATUS_BANNED], $names[static::STATUS_DELETED]);
		return $names;
	}

	public static function getRolesNames(): array {
		return [];
	}

	public static function getPermissionsNames(): array {
		return [];
	}

}
