<?php

namespace common\fixtures\provision;

use common\fixtures\settlement\PayFixture;
use common\fixtures\UserFixture;
use common\models\provision\Provision;
use yii\test\ActiveFixture;

class ProvisionFixture extends ActiveFixture {

	public $modelClass = Provision::class;

	public $depends = [
		UserFixture::class,
		ProvisionTypeFixture::class,
		PayFixture::class,
	];
}
