<?php

namespace common\modules\lead\exceptions;

use Throwable;

class MissingDataAttributeException extends BaseException {

	private string $attribute;

	public function __construct(string $attribute, $message = "", $code = 0, Throwable $previous = null) {
		$this->attribute = $attribute;
		if ($message === "") {
			$message = $this->getName();
		}
		parent::__construct($message, $code, $previous);
	}

	public function getName(): string {
		return 'Missing ' . $this->attribute . ' in lead data.';
	}
}
