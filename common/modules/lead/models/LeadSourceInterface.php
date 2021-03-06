<?php

namespace common\modules\lead\models;

interface LeadSourceInterface {

	public function getID(): string;

	public function getName(): string;

	public function getType(): LeadTypeInterface;

	public function getPhone(): ?string;

	public function getURL(): ?string;

	public function getOwnerId(): ?int;

}
