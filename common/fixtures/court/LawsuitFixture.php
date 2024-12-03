<?php

namespace common\fixtures\court;

use common\fixtures\issue\IssueFixture;
use common\fixtures\UserFixture;
use common\modules\court\models\Lawsuit;
use yii\test\ActiveFixture;

class LawsuitFixture extends ActiveFixture {

	public $modelClass = Lawsuit::class;

	public $depends = [
		CourtFixture::class,
		IssueFixture::class,
		UserFixture::class,
	];
}
