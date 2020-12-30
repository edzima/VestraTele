<?php

namespace common\fixtures\issue;

use common\models\issue\Summon;
use yii\test\ActiveFixture;

class SummonFixture extends ActiveFixture {

	public $modelClass = Summon::class;

	public $depends = [
		IssueFixture::class,
	];

}
