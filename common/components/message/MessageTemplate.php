<?php

namespace common\components\message;

use ymaker\email\templates\models\EmailTemplate;

class MessageTemplate extends EmailTemplate {

	public ?string $primaryButtonText = null;
	public ?string $primaryButtonHref = null;

	private $key;

	protected function setKey(string $value): void {
		$this->key = $value;
	}

	public function getKey(): string {
		return $this->key;
	}

	public static function buildFromEntity($entity): self {
		$model = new self($entity->subject, $entity->body);
		$model->setKey($entity->template->key);
		return $model;
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
		$message = str_replace(["&nbsp;", ' '], ' ', $message);
		return $message;
	}
}
