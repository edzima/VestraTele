<?php

namespace common\modules\lead\models;

interface ReportSchemaInterface {

	public const FIRSTNAME_ID = 1;
	public const LASTNAME_ID = 2;

	public function getName(): string;

	public function getPlaceholder(): ?string;

}
