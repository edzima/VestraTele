<?php

namespace common\modules\calendar\controllers;

use common\models\CalendarNews;

class LeadCalendarNoteController extends CalendarNoteController {

	protected function getType(): string {
		return CalendarNews::TYPE_LEAD_REMINDER;
	}
}
