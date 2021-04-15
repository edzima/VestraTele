<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\Lead;
use yii\test\ActiveFixture;

class LeadFixture extends ActiveFixture {

	public $modelClass = Lead::class;

	public $depends = [
		SourceFixture::class,
		StatusFixture::class,
		UserFixture::class,
	];
}
