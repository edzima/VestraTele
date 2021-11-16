<?php

namespace common\fixtures\message;

use yii\test\ActiveFixture;
use ymaker\email\templates\entities\EmailTemplate;

class MessageTemplateFixture extends ActiveFixture {

	public $modelClass = EmailTemplate::class;

	public function afterLoad() {
		parent::afterLoad();
		codecept_debug($this->getData());
	}
}
