<?php

namespace backend\widgets;

use common\widgets\grid\IssueColumn as BaseIssueColumn;

class IssueColumn extends BaseIssueColumn {

	public string $viewBaseUrl = '/issue/issue/view';
}
