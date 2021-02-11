<?php

namespace common\fixtures\lead;

use common\modules\lead\models\Lead;
use yii\test\ActiveFixture;

class LeadFixture extends ActiveFixture {

	public $modelClass = Lead::class;

	public $depends = [
		LeadUserFixture::class,
		LeadStatusFixture::class,
		LeadTypeFixture::class,
	];
}
