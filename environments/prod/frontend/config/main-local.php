<?php

$config = [
	'components' => [
		'request' => [
			// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
			'cookieValidationKey' => '',
		],
	],
];

$maintenance = getenv("FRONTEND_MAINTENANCE");
if (strtotime($maintenance)) {
	$config['bootstrap'] = ['maintenance'];
	$config['components']['maintenance'] = [
		'class' => 'common\components\maintenance\Maintenance',
		'enabled' => true,
		'time' => $maintenance,
		'route' => 'maintenance/index',
		'message' => Yii::t('common', 'Technical break'),
	];
}

return $config;
