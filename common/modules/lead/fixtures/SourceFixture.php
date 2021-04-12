<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadSource;
use yii\test\ActiveFixture;

class SourceFixture extends ActiveFixture {

	public $modelClass = LeadSource::class;

	public $depends = [
		UserFixture::class,
	];
}
