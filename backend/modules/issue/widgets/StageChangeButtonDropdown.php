<?php

namespace backend\modules\issue\widgets;

use backend\modules\issue\controllers\IssueController;
use common\modules\issue\widgets\StageChangeButtonDropdown as BaseStageChangeButtonDropdown;

class StageChangeButtonDropdown extends BaseStageChangeButtonDropdown {

	/** @see IssueController::actionStage() */
	public string $route = '/issue/issue/stage';

}
