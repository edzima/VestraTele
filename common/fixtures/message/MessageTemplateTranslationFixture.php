<?php

namespace common\fixtures\message;

use yii\test\ActiveFixture;
use ymaker\email\templates\entities\EmailTemplate;
use ymaker\email\templates\entities\EmailTemplateTranslation;

class MessageTemplateTranslationFixture extends ActiveFixture {

	public $modelClass = EmailTemplateTranslation::class;
	public $depends = [MessageTemplateFixture::class];
}
