<?php

namespace common\modules\lead\models;

use DateTime;

interface LeadInterface {

	public function getDateTime(): DateTime;

	public function getData(): array;

	public function getSource(): string;

	public function getPhone(): ?string;

	public function getEmail(): ?string;

	public function getPostalCode(): ?string;
}
