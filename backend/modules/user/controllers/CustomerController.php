<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\CustomerUserForm;
use backend\modules\user\models\search\CustomerUserSearch;
use common\models\user\Customer;
use common\models\user\User;

class CustomerController extends UserController {

	public $accessRole = User::ROLE_CLIENT;
	public $searchModel = CustomerUserSearch::class;
	public $formModel = CustomerUserForm::class;
	public $model = Customer::class;
}
