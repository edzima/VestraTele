<?php

namespace common\models\issue\search;

use common\models\issue\IssueType;
use yii\db\ActiveQuery;

interface IssueParentTypeSearchable {

	public function getIssueParentType(): ?IssueType;

	public function applyIssueParentTypeFilter(ActiveQuery $query): void;
}
