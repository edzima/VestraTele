<?php

namespace frontend\widgets;

use common\modules\issue\widgets\IssueTypeNav as BaseIssueTypeNav;
use frontend\helpers\Url;

class IssueTypeNav extends BaseIssueTypeNav {

	public array $route = [Url::ROUTE_ISSUE_INDEX];

	public bool $onlyUserIssues = true;

}
