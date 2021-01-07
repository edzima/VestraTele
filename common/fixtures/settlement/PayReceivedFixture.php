<?php

namespace common\fixtures\settlement;

use common\fixtures\UserFixture;
use common\models\settlement\PayReceived;
use yii\test\ActiveFixture;

class PayReceivedFixture extends ActiveFixture {

	public $modelClass = PayReceived::class;

	public $depends = [
		UserFixture::class,
		PayFixture::class,
	];
}
