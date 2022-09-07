<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadQuestion;
use yii\test\ActiveFixture;

class LeadQuestionFixture extends ActiveFixture {

	public $modelClass = LeadQuestion::class;

	public $depends = [
		StatusFixture::class,
		TypeFixture::class,
	];
}
