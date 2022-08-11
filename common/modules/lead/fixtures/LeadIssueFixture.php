<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadIssue;
use yii\test\ActiveFixture;

class LeadIssueFixture extends ActiveFixture {

	public $modelClass = LeadIssue::class;

	public $depends = [
		LeadFixture::class,
		LeadCrmFixture::class,
	];
}
