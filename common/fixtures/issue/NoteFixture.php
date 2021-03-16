<?php

namespace common\fixtures\issue;

use common\fixtures\UserFixture;
use common\models\issue\IssueNote;
use yii\test\ActiveFixture;

class NoteFixture extends ActiveFixture {

	public $modelClass = IssueNote::class;

	public $depends = [
		IssueFixture::class,
		UserFixture::class,
	];
}
