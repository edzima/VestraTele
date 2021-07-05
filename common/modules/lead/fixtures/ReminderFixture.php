<?php

namespace common\modules\lead\fixtures;

use common\fixtures\ReminderFixture as BaseReminderFixture;
use common\modules\lead\models\LeadReminder;
use yii\test\ActiveFixture;

class ReminderFixture extends ActiveFixture {

	public $modelClass = LeadReminder::class;

	public $depends = [
		LeadFixture::class,
		BaseReminderFixture::class,
	];
}
