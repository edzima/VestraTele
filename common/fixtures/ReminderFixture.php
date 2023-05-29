<?php

namespace common\fixtures;

use common\modules\reminder\models\Reminder;
use yii\test\ActiveFixture;

class ReminderFixture extends ActiveFixture {

	public $modelClass = Reminder::class;

	public $depends = [
		UserFixture::class,
	];
}
