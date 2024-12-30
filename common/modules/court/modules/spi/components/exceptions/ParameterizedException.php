<?php

namespace common\modules\court\modules\spi\components\exceptions;

class ParameterizedException extends SPIApiException {

	public array $params;

	public static function createFromResponseData(array $data): static {
		$self = parent::createFromResponseData($data);
		$self->params = $data['params'];
		return $self;
	}
}
