<?php

namespace common\fixtures\user;

use common\models\user\Customer;

class CustomerFixture extends RbacUserFixture {

	public $modelClass = Customer::class;
	public array $roles = [Customer::ROLE_DEFAULT];

}
