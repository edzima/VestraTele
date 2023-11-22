<?php

use yii\db\Expression;

return [
	[
		'issue_id' => 100,
		'shipment_number' => 'testp0',
		'created_at' => new Expression('CURRENT_TIMESTAMP'),
		'updated_at' => new Expression('CURRENT_TIMESTAMP'),
	],
];
