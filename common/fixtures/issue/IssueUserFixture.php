<?php

namespace common\fixtures\issue;

use common\fixtures\user\AgentFixture;
use common\fixtures\user\CustomerFixture;
use common\fixtures\user\LawyerFixture;
use common\fixtures\user\TelemarketerFixture;
use common\fixtures\UserFixture;
use common\models\issue\IssueUser;
use yii\test\ActiveFixture;

class IssueUserFixture extends ActiveFixture {

	public $modelClass = IssueUser::class;

	public $depends = [
		IssueFixture::class,
		CustomerFixture::class,
		AgentFixture::class,
		LawyerFixture::class,
		TelemarketerFixture::class,
	];

}
