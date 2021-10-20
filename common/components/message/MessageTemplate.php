<?php

namespace common\components\message;

use ymaker\email\templates\models\EmailTemplate;

class MessageTemplate extends EmailTemplate {

	public static function buildFromEntity($entity): self {
		return new self($entity->subject, $entity->body);
	}
}
