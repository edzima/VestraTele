<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadReportSchemaStatusType;
use yii\test\ActiveFixture;

class LeadReportSchemaStatusTypeFixture extends ActiveFixture {

	public $modelClass = LeadReportSchemaStatusType::class;

	public $depends = [
		LeadReportSchemaFixture::class,
		TypeFixture::class,
		StatusFixture::class
	];
}
