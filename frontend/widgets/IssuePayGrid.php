<?php

namespace frontend\widgets;

use common\widgets\grid\IssuePayGrid as BaseIssuePayGrid;

class IssuePayGrid extends BaseIssuePayGrid {

	public ?string $payProvisionsRoute = '/pay/pay-provisions';
	public ?string $statusRoute = null;
	public ?string $updateRoute = '/pay/update';
	public ?string $deleteRoute = null;
	public ?string $payRoute = null;

}
