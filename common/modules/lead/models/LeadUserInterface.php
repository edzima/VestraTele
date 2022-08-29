<?php

namespace common\modules\lead\models;

interface LeadUserInterface {

	public function getID(): int;

	public function getFullName(): string;

	public function getEmail(): string;
}
