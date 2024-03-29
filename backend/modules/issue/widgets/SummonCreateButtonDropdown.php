<?php

namespace backend\modules\issue\widgets;

use backend\modules\issue\controllers\SummonController;
use common\modules\issue\widgets\SummonCreateButtonDropdown as BaseSummonCreateDropdown;

class SummonCreateButtonDropdown extends BaseSummonCreateDropdown {

	/** @see SummonController::actionCreate() */
	public string $route = '/issue/summon/create';

	public string $issueViewRoute = '/issue/issue/view';
}
