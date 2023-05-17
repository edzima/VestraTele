<?php

return [
	[
		'id' => 1,
		'name' => 'wordpress-homepage',
		'type_id' => 1,
		'phone' => '48 123-123-123',
		'dialer_phone' => '810',
		'is_active' => true,
		'sms_push_template' => 'Thank for your request. Check or call to {sourcePhone.',
	],
	[
		'id' => 2,
		'name' => 'agent-1-personal-page',
		'type_id' => 1,
		'owner_id' => 1,
		'is_active' => true,
	],
	[
		'id' => 3,
		'name' => 'facebook-fan-page',
		'type_id' => 2,
		'dialer_phone' => '820',
		'phone' => '48 123-123-123',
		'is_active' => true,
	],
	[
		'id' => 4,
		'name' => 'facebook-old-fan-page',
		'type_id' => 2,
		'dialer_phone' => '820',
		'is_active' => false,
	],
];
