<?php

use backend\modules\issue\models\IssueStage;

return [
	[
		'type_id' => 1,
		'stage_id' => 1,
		'entity_responsible_id' => 1,
		'signing_at' => '2020-01-01',
	],
	[
		'type_id' => 1,
		'stage_id' => 2,
		'entity_responsible_id' => 1,
		'signing_at' => '2020-01-02',
	],
	[
		'type_id' => 2,
		'stage_id' => 2,
		'entity_responsible_id' => 1,
		'signing_at' => '2020-01-01',
	],
	[
		'type_id' => 2,
		'stage_id' => 3,
		'entity_responsible_id' => 2,
		'signing_at' => '2020-01-02',
	],
	[
		'type_id' => 3,
		'stage_id' => 2,
		'entity_responsible_id' => 2,
		'signing_at' => '2019-01-02',
		'type_additional_date_at' => '2020-01-02',
	],
	'archived' => [
		'type_id' => 1,
		'stage_id' => IssueStage::ARCHIVES_ID,
		'entity_responsible_id' => 1,
		'archives_nr' => 'A1000',
		'signing_at' => '2019-01-02',
	],

];
