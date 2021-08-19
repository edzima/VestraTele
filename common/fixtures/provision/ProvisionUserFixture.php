<?php

namespace common\fixtures\provision;

use common\fixtures\UserFixture;
use common\models\provision\ProvisionUser;
use yii\test\ActiveFixture;

class ProvisionUserFixture extends ActiveFixture {

	public $modelClass = ProvisionUser::class;

	public $depends = [
		UserFixture::class,
		ProvisionTypeFixture::class,
	];
}
