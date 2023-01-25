<?php

namespace common\modules\calendar\controllers;

use common\models\CalendarNews;

class IssueStageDeadlineCalendarNoteController extends CalendarNoteController {

	public bool $alwaysWithoutUser = true;

	protected function getType(): string {
		return CalendarNews::TYPE_ISSUE_STAGE_DEADLINE;
	}

}
