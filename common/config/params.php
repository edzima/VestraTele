<?php

return [
    'adminEmail' => getenv('ADMIN_EMAIL'),
    'robotEmail' => getenv('ROBOT_EMAIL'),
	'formatter' => [
    'class' => 'yii\i18n\Formatter',
    'dateFormat' => 'php:d-M-Y',
    'datetimeFormat' => 'php:d-M-Y H:i:s',
    'timeFormat' => 'php:H:i:s',
]
];
