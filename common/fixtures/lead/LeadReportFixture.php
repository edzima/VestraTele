<?php

namespace common\fixtures\lead;

use common\modules\lead\models\LeadReport;
use yii\test\ActiveFixture;

class LeadReportFixture extends ActiveFixture {

	public $modelClass = LeadReport::class;

	public $depends = [
		LeadFixture::class,
		LeadUserFixture::class,
	];
}
