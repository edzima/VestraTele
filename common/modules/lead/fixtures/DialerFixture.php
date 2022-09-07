<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadDialer;
use yii\test\ActiveFixture;

class DialerFixture extends ActiveFixture {

	public $modelClass = LeadDialer::class;

	public $depends = [
		LeadFixture::class,
		DialerTypeFixture::class,
	];
}
