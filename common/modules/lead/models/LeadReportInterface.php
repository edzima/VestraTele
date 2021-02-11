<?php

namespace common\modules\lead\models;

interface LeadReportInterface {

	public function getLead(): LeadInterface;

	public function getSchema(): ReportSchemaInterface;

	public function getStatus(): LeadStatusInterface;

	public function getOldStatus(): ?LeadStatusInterface;

	public function getOwnerId(): int;

	public function getDetails(): ?string;
}
