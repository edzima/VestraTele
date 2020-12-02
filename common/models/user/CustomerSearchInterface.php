<?php

namespace common\models\user;

use yii\db\QueryInterface;

interface CustomerSearchInterface {

	public function applyCustomerSurnameFilter(QueryInterface $query): void;
}
