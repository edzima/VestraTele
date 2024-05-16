<?php

use common\models\court\Court;

return [
	[
		'id' => 1,
		'name' => 'Sąd Apelacyjny w Białymstoku',
		'type' => Court::TYPE_APPEAL,
		'phone' => '(85) 743 07 27',
		'fax' => '85 743 02 21',
		'email' => 'boi@bialystok.sa.gov.pl',
		'updated_at' => '2024-02-27',
	],
	[
		'id' => 2,
		'name' => 'Sąd Okręgowy w Białymstoku',
		'type' => Court::TYPE_REGIONAL,
		'phone' => '(85) 742 23 46',
		'fax' => '(85) 742 46 40',
		'email' => 'boi@bialystok.so.gov.pl',
		'parent_id' => 1,
		'updated_at' => '2024-02-27',
	],
	[
		'id' => 3,
		'name' => 'Sąd Rejonowy w Białymstoku',
		'type' => Court::TYPE_DISTRICT,
		'phone' => '(85) 742 23 46',
		'fax' => '(85) 665 63 33',
		'email' => 'boi@bialystok.so.gov.pl',
		'parent_id' => 3,
		'updated_at' => '2024-02-27',
	],
];
