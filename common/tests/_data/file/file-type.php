<?php

use common\modules\file\models\FileType;
use common\modules\file\models\ValidatorOptions;

return [
	[
		'name' => 'Agreement-Private',
		'visibility' => FileType::VISIBILITY_PRIVATE,
		'validator_config' => (new ValidatorOptions([
			'extensions' => 'jpg,png',
			'maxFiles' => 1,
		])
		)->toJson(),
	],
	[
		'name' => 'Agreement-Public',
		'visibility' => FileType::VISIBILITY_PUBLIC,
		'validator_config' => (new ValidatorOptions([
			'extensions' => 'jpg,png',
			'maxSize' => 20000,
			'maxFiles' => 3,
		])
		)->toJson(),
	],
];
