<?php

use common\fixtures\helpers\UserFixtureHelper;

return [
	[
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_WAYNE_ID,
		'firstname' => 'John',
		'lastname' => 'Wayne',
		'phone' => '+48 673 222 110',
		'phone_2' => '',
	],
	[
		'user_id' => UserFixtureHelper::CUSTOMER_ERIKA_LARSON_ID,
		'firstname' => 'Erika',
		'lastname' => 'Larson',
		'phone' => '+48 682 222 110',
		'phone_2' => '',
	],
	[
		'user_id' => UserFixtureHelper::CUSTOMER_TOMMY_JOHNS,
		'firstname' => 'Tommy',
		'lastname' => 'Johns',
		'phone' => '+48 555 222 110',
	],
	[
		'user_id' => UserFixtureHelper::CUSTOMER_JOHN_LENNON,
		'firstname' => 'John',
		'lastname' => 'Lennon',
	],
];
