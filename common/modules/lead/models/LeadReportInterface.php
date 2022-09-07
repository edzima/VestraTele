<?php

namespace common\modules\lead\models;

interface LeadReportInterface {

	public function getLead(): LeadInterface;

	public function getStatus(): LeadStatusInterface;

	public function getOldStatus(): ?LeadStatusInterface;

	public function getOwnerId(): int;

	public function getDetails(): ?string;

	public function getAnswersNames(): array;
}
