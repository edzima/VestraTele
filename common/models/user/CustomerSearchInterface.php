<?php

namespace common\models\user;

use yii\db\QueryInterface;

interface CustomerSearchInterface extends SurnameSearchInterface {
	
	public function applyCustomerSurnameFilter(QueryInterface $query): void;
}
