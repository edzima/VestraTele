<?php

namespace common\modules\lead\models;

interface LeadTypeInterface {

	public function getID(): int;

	public function getName(): string;

}
