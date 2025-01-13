<?php

use common\modules\lead\models\LeadQuestion;

return [
	[
		'name' => 'Firstname',
		'placeholder' => 'Firstname',
		'show_in_grid' => true,
		'type' => LeadQuestion::TYPE_TEXT,
	],
	[
		'name' => 'Lastname',
		'placeholder' => 'Lastname',
		'show_in_grid' => true,
		'type' => LeadQuestion::TYPE_TEXT,
	],
	[
		'name' => 'Works - will not quit',
		'type' => LeadQuestion::TYPE_TAG,
	],
	[
		'name' => 'Works - will quit',
		'type' => LeadQuestion::TYPE_TAG,
	],
	[
		'name' => 'No judgment, maybe it will',
		'type' => LeadQuestion::TYPE_TAG,
	],
	[
		'name' => 'No judgment, it will not',
		'type' => LeadQuestion::TYPE_TAG,
	],
	[
		'name' => 'Bool Quest',
		'type' => LeadQuestion::TYPE_BOOLEAN,
	],
	'only-accident' => [
		'name' => 'Died',
		'type_id' => 1,
		'type' => LeadQuestion::TYPE_TEXT,
	],
];
