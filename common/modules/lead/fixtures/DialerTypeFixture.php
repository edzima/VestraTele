<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadDialerType;
use yii\test\ActiveFixture;

class DialerTypeFixture extends ActiveFixture {

	public $modelClass = LeadDialerType::class;

	public $depends = [
		UserFixture::class,
	];
}
