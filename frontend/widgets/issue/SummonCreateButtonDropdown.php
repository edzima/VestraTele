<?php

namespace frontend\widgets\issue;

use common\modules\issue\widgets\SummonCreateButtonDropdown as BaseSummonCreateDropdown;
use frontend\controllers\SummonController;

class SummonCreateButtonDropdown extends BaseSummonCreateDropdown {

	/** @see SummonController::actionCreate() */
	public string $route = '/summon/create';

	public string $issueViewRoute = '/issue/view';

}
