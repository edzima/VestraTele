<?php

namespace common\fixtures\issue;

use common\models\issue\Issue;
use yii\test\ActiveFixture;

class IssueFixture extends ActiveFixture {

	public $modelClass = Issue::class;

	public $depends = [
		StageTypesFixtures::class,
		EntityResponsibleFixture::class,
	];

}
