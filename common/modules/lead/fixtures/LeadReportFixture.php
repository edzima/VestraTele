<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadReport;
use yii\test\ActiveFixture;

class LeadReportFixture extends ActiveFixture {

	public $modelClass = LeadReport::class;

	public $depends = [
		LeadFixture::class,
		UserFixture::class,
	];
}
