<?php

namespace common\fixtures\user;

use common\fixtures\AddressFixture;
use common\fixtures\UserFixture;
use common\models\user\UserAddress;
use yii\test\ActiveFixture;

class UserAddressFixture extends ActiveFixture {

	public $modelClass = UserAddress::class;

	public $depends = [
		UserFixture::class,
		AddressFixture::class
	];
}
