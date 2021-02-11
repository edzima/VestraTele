<?php

namespace common\modules\lead\models;

interface LeadStatusInterface {

	public const STATUS_NEW = 1;
	public const STATUS_ARCHIVE = 100;

	public function getName(): string;

}
