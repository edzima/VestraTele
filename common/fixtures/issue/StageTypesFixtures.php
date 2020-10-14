<?php

namespace common\fixtures\issue;

use yii\test\ActiveFixture;

class StageTypesFixtures extends ActiveFixture {

	public $tableName = '{{%issue_stage_type}}';
	public $depends = [
		StageFixture::class,
	];
}
