<?php

namespace common\fixtures\issue;

use common\models\issue\IssueRelation;
use yii\test\ActiveFixture;

class IssueRelationFixture extends ActiveFixture {

	public $modelClass = IssueRelation::class;

	public $depends = [
		IssueFixture::class,
	];
}
