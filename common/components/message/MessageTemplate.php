<?php

namespace common\components\message;

use ymaker\email\templates\models\EmailTemplate;

class MessageTemplate extends EmailTemplate {

	public static function buildFromEntity($entity): self {
		return new self($entity->subject, $entity->body);
	}

	public function getSmsMessage(): string {
		$message = $this->getBody();
		$message = str_replace([
			'</p><p>',
			'<br>',
		],
			"\n",
			$message);

		$message = strip_tags($message);
		return $message;
	}
}
