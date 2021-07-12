<?php

namespace common\modules\lead\models;

interface ReportSchemaInterface {

	public function getName(): string;

	public function getPlaceholder(): ?string;

}
