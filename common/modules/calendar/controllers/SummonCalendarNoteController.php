<?php

namespace common\modules\calendar\controllers;

use common\models\CalendarNews;
use common\models\user\Worker;
use Yii;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;

class SummonCalendarNoteController extends CalendarNoteController {

	public function actionList(string $start = null, string $end = null, int $userId = null): Response {
		if ($userId !== null
			&& $userId !== Yii::$app->user->getId()
			&& !Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
		) {
			throw new MethodNotAllowedHttpException();
		}
		return parent::actionList($start, $end, $userId);
	}

	protected function getType(): string {
		return CalendarNews::TYPE_SUMMON;
	}

	protected function allowDelete(CalendarNews $model): bool {
		return parent::allowDelete($model) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER);
	}

}
