<?php

namespace common\components\message;

use ymaker\email\templates\models\EmailTemplate;

interface IssueMessageManager {

	public function getIssueTypeTemplatesLikeKey(string $key, int $typeId, string $language = null): ?EmailTemplate;
}
