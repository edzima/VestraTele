<?php

namespace common\fixtures\email;

use yii\test\ActiveFixture;
use ymaker\email\templates\entities\EmailTemplate;

class EmailTemplateFixture extends ActiveFixture {

	public $modelClass = EmailTemplate::class;
}
