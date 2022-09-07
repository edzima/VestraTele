<?php

use common\models\Article;

return [
	'Active show_on_mainpage' => [
		'title' => 'Visible on Mainpage Article',
		'show_on_mainpage' => 1,
		'status' => Article::STATUS_ACTIVE,
		'published_at' => '2020-01-01',
		'category_id' => 1,
	],
	'Draft show_on_mainpage' => [
		'title' => 'Draft on Mainpage Article',
		'show_on_mainpage' => 2,
		'status' => Article::STATUS_DRAFT,
		'published_at' => '2020-01-01',
		'category_id' => 1,
	],

];
