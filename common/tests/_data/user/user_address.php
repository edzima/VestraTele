<?php

use common\models\user\UserAddress;

return [
	[
		'address_id' => 1,
		'type' => UserAddress::TYPE_HOME,
		'user_id' => 1,
	],
	[
		'address_id' => 1,
		'type' => UserAddress::TYPE_HOME,
		'user_id' => 2,
	],
	[
		'address_id' => 2,
		'type' => UserAddress::TYPE_HOME,
		'user_id' => 3,
	],
	[
		'address_id' => 2,
		'type' => UserAddress::TYPE_HOME,
		'user_id' => 4,
	],

];
