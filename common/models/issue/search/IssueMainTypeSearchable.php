<?php

namespace common\models\issue\search;

use common\models\issue\IssueType;
use yii\db\ActiveQuery;

interface IssueMainTypeSearchable {

	public function getIssueMainType(): ?IssueType;

	public function applyIssueMainTypeFilter(ActiveQuery $query): void;
}
