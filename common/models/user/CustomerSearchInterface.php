<?php

namespace common\models\user;

use yii\db\QueryInterface;

interface CustomerSearchInterface extends SurnameSearchInterface {
	
	public function applyCustomerNameFilter(QueryInterface $query): void;
}
