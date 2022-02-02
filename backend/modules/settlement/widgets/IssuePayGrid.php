<?php

namespace backend\modules\settlement\widgets;

use common\widgets\grid\IssuePayGrid as BaseIssuePayGrid;

class IssuePayGrid extends BaseIssuePayGrid {

	public ?string $receivedRoute = null;
}
