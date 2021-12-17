<?php

namespace frontend\widgets\issue;

use common\modules\issue\widgets\StageChangeButtonDropdown as BaseStageChangeButtonDropdown;
use frontend\controllers\IssueController;

class StageChangeButtonDropdown extends BaseStageChangeButtonDropdown {

	/** @see IssueController::actionStage() */
	public string $route = '/issue/stage';

}
