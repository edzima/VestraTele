<?php

namespace backend\modules\user\models\search;

use common\models\user\Customer;
use common\models\user\query\UserQuery;

class CustomerUserSearch extends UserSearch {

	protected function createQuery(): UserQuery {
		return Customer::find();
	}

}
