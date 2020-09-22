<?php

use common\components\maintenance\Maintenance;

$config = [
	'components' => [
		'request' => [
			// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
			'cookieValidationKey' => '',
		],
	],
];

// maintenance mode
$config['bootstrap'] = ['maintenance'];
$config['components']['maintenance'] = [
	'class' => Maintenance::class,
	'enabled' => static function ($app) {
		return $app->keyStorage->get('frontend.maintenance');
	},
	'route' => 'maintenance/index',
	'message' => 'Przerwa techniczna',
	// year-month-day hour:minute:second
	'time' => '0000-00-00 00:00:00', // время окончания работ
];

return $config;
