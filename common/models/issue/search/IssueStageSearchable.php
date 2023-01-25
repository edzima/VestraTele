<?php

namespace common\models\issue\search;

use yii\db\QueryInterface;

interface IssueStageSearchable {

	public function getIssueStagesNames(): array;

	public function applyIssueStageFilter(QueryInterface $query): void;
}
