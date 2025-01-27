<?php

namespace common\modules\court\modules\spi\entity\application;

interface ApplicationStatus {

	public const APPLICATION_STATUS_REJECTED = 5;
	public const APPLICATION_STATUS_ACCEPTED = 7;

	public const APPLICATION_STATUS_REGISTERED = 10;

	public const APPLICATION_STATUS_DELETED = 13;

	public const APPLICATION_STATUS_COMPLAIN = 14;
	public const APPLICATION_STATUS_COMPLAIN_ACCEPTED = 15;
	public const APPLICATION_STATUS_COMPLAIN_REJECTED = 16;

	public function getApplicationStatus(): int;
}
