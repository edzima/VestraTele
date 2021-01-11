<?php

namespace common\fixtures\issue;

use common\fixtures\teryt\SimcFixture;
use common\models\issue\Summon;
use yii\test\ActiveFixture;

class SummonFixture extends ActiveFixture {

	public $modelClass = Summon::class;

	public $depends = [
		IssueFixture::class,
		SimcFixture::class,
	];

}
