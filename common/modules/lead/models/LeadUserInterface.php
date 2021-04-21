<?php

namespace common\modules\lead\models;

interface LeadUserInterface {

	public function getID(): string;

	public function getFullName(): string;
}
