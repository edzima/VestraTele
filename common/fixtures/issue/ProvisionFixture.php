<?php

namespace common\fixtures\issue;

use common\models\issue\IssueClaim;
use yii\test\ActiveFixture;

class ProvisionFixture extends ActiveFixture {

	public $modelClass = IssueClaim::class;

	public $depends = [
		IssueFixture::class,
	];
}
