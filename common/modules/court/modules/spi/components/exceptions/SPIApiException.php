<?php

namespace common\modules\court\modules\spi\components\exceptions;

use yii\base\Exception as BaseException;

class SPIApiException extends BaseException {

	public string $type;

	public static function createFromResponseData(array $data): static {
		$self = new static();
		$self->message = $data['title'];
		$self->code = $data['status'];
		$self->type = $data['type'];
		return $self;
	}
}
