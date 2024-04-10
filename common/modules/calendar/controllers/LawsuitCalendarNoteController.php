<?php

namespace common\modules\calendar\controllers;

use common\models\CalendarNews;

class LawsuitCalendarNoteController extends CalendarNoteController {

	protected function getType(): string {
		return CalendarNews::TYPE_LAWSUIT;
	}
}
