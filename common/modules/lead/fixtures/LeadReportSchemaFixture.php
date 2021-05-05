<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadReportSchema;
use yii\test\ActiveFixture;

class LeadReportSchemaFixture extends ActiveFixture {

	public $modelClass = LeadReportSchema::class;

	public $depends = [
		StatusFixture::class,
		TypeFixture::class,
	];
}
