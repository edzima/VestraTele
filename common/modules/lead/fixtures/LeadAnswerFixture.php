<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadAnswer;
use yii\test\ActiveFixture;

class LeadAnswerFixture extends ActiveFixture {

	public $modelClass = LeadAnswer::class;

	public $depends = [
		LeadReportFixture::class,
		LeadQuestionFixture::class,
	];

}
