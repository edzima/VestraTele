<?php

use common\models\user\Worker;
use yii\helpers\Json;

return [
	[
		'name' => 'Default',
		'is_percentage' => 1,
		'value' => 25,
	],
	[
		'name' => 'Meeting',
		'is_percentage' => 1,
		'value' => 5,
		'data' => Json::encode(
			['roles' => Worker::ROLE_TELEMARKETER]
		),
	],

];
