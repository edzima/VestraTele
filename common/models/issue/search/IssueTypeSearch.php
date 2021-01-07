<?php

namespace common\models\issue\search;

use yii\db\QueryInterface;

interface IssueTypeSearch {

	public function applyIssueTypeFilter(QueryInterface $query): void;

	public static function getIssueTypesNames(): array;
}
