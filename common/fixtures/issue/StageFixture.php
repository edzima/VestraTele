<?php

namespace common\fixtures\issue;

use common\models\issue\IssueStage;
use yii\test\ActiveFixture;

class StageFixture extends ActiveFixture {

	public $modelClass = IssueStage::class;

	public $depends = [
		TypeFixture::class,
	];

}
