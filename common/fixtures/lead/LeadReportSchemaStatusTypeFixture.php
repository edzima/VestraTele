<?php

namespace common\fixtures\lead;

use common\modules\lead\models\LeadReportSchemaStatusType;
use yii\test\ActiveFixture;

class LeadReportSchemaStatusTypeFixture extends ActiveFixture {

	public $modelClass = LeadReportSchemaStatusType::class;

	public $depends = [
		LeadStatusFixture::class,
		LeadTypeFixture::class,
	];
}
