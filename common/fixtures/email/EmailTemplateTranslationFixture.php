<?php

namespace common\fixtures\email;

use yii\test\ActiveFixture;
use ymaker\email\templates\entities\EmailTemplate;
use ymaker\email\templates\entities\EmailTemplateTranslation;

class EmailTemplateTranslationFixture extends ActiveFixture {

	public $modelClass = EmailTemplateTranslation::class;
	public $depends = [EmailTemplateFixture::class];
}
