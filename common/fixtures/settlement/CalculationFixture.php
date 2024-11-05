<?php

namespace common\fixtures\settlement;

use common\fixtures\issue\IssueFixture;
use common\fixtures\issue\TypeFixture;
use common\models\issue\IssuePayCalculation;
use yii\test\ActiveFixture;

class CalculationFixture extends ActiveFixture {

	public $modelClass = IssuePayCalculation::class;

	public $depends = [
		TypeFixture::class,
		CostFixture::class,
		IssueFixture::class,
	];
}
