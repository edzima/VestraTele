<?php

use common\models\ArticleCategory;

return [
	[
		'title' => 'Active 1',
		'slug' => 'active_1',
		'status' => ArticleCategory::STATUS_ACTIVE,
	],
	[
		'title' => 'Active 2',
		'slug' => 'active_2',
		'status' => ArticleCategory::STATUS_ACTIVE,
	],
	[
		'title' => 'Dratft',
		'slug' => 'draft',
		'status' => ArticleCategory::STATUS_DRAFT,
	],
];
