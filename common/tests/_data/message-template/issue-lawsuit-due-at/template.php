<?php

use common\models\message\IssueLawsuitDueAtSmsForm;

return [
	[
		'id' => 1,
		'key' => IssueLawsuitDueAtSmsForm::generateKey(
			IssueLawsuitDueAtSmsForm::TYPE_SMS,
			IssueLawsuitDueAtSmsForm::keyCustomer(),
			[1]
		),
	],
	[
		'id' => 2,
		'key' => IssueLawsuitDueAtSmsForm::generateKey(
			IssueLawsuitDueAtSmsForm::TYPE_SMS,
			IssueLawsuitDueAtSmsForm::keyCustomer(),
			[2]
		),
	],
	[
		'id' => 3,
		'key' => IssueLawsuitDueAtSmsForm::generateKey(
			IssueLawsuitDueAtSmsForm::TYPE_EMAIL,
			IssueLawsuitDueAtSmsForm::keyCustomer(),
			[1, 2]
		),
	],
	[
		'id' => 4,
		'key' => IssueLawsuitDueAtSmsForm::generateKey(
			IssueLawsuitDueAtSmsForm::TYPE_SMS,
			IssueLawsuitDueAtSmsForm::keyWorkers(),
			[1]
		),
	],
	[
		'id' => 5,
		'key' => IssueLawsuitDueAtSmsForm::generateKey(
			IssueLawsuitDueAtSmsForm::TYPE_EMAIL,
			IssueLawsuitDueAtSmsForm::keyWorkers(),
		),
	],
];
