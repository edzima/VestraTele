<?php

namespace common\modules\lead\models;

use DateTime;

interface LeadInterface {

	public function getStatusId(): int;

	public function getSourceId(): int;

	public function getTypeId(): int;

	public function getDateTime(): DateTime;

	public function getData(): array;

	public function getPhone(): ?string;

	public function getEmail(): ?string;

	public function getPostalCode(): ?string;

}
