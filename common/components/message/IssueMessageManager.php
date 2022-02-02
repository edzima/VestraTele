<?php

namespace common\components\message;

interface IssueMessageManager {

	public function getIssueTypeTemplatesLikeKey(string $key, int $typeId, string $language = null): ?MessageTemplate;
}
