<?php

namespace common\fixtures\lead;

use common\modules\lead\models\LeadReportSchema;
use yii\test\ActiveFixture;

class LeadReportSchemaFixture extends ActiveFixture {

	public $modelClass = LeadReportSchema::class;

	public $depends = [
		LeadReportSchemaStatusTypeFixture::class,
	];
}
