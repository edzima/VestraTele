<?php

namespace frontend\helpers;

use common\helpers\Url as BaseUrl;

class Url extends BaseUrl {

	protected const ROUTE_ISSUE_VIEW = '/issue/view';
	protected const ROUTE_SETTLEMENT_VIEW = '/settlement/view';

	protected static function managerConfig(): array {
		return require __DIR__ . '/../config/_urlManager.php';
	}

}
