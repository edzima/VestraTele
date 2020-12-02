<?php

namespace common\fixtures\settlement;

use common\models\issue\IssuePay;
use yii\test\ActiveFixture;

class PayFixture extends ActiveFixture {

	public $modelClass = IssuePay::class;

	public $depends = [
		CalculationFixture::class,
	];
}
