<?php

namespace common\modules\calendar\controllers;

use common\helpers\ArrayHelper;
use common\modules\calendar\models\searches\LeadStatusDeadlineSearch;
use common\modules\lead\models\searches\LeadReminderSearch;
use common\modules\reminder\models\Reminder;
use DateTime;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class LeadReminderController extends Controller {

	public function runAction($id, $params = []) {
		if ($id !== 'index') {
			$params = ArrayHelper::merge(Yii::$app->getRequest()->getBodyParams(), $params);
		}
		return parent::runAction($id, $params);
	}

	public function actionIndex(): string {
		return $this->render('index');
	}

	public function actionList(string $start = null, string $end = null): Response {
		$this->ensureStartAndAt($start, $end);
		$model = new LeadReminderSearch();
		$model->scenario = LeadReminderSearch::SCENARIO_USER;
		$model->onlyDelayed = null;
		$model->hideDone = false;
		$model->leadUserId = Yii::$app->user->getId();
		$model->dateStart = $start;
		$model->dateEnd = $end;
		return $this->asJson($model->getEventsData());
	}

	public function actionStatusDeadline(string $start = null, string $end = null): Response {
		$this->ensureStartAndAt($start, $end);

		$model = new LeadStatusDeadlineSearch();
		$model->startAt = $start;
		$model->endAt = $end;
		if (YII_IS_FRONTEND) {
			$model->scenario = LeadStatusDeadlineSearch::SCENARIO_USER;
			$model->leadUserId = Yii::$app->user->getId();
		}

		return $this->asJson($model->getEventsData());
	}

	protected function ensureStartAndAt(string &$start = null, string &$end = null): void {
		if ($start === null) {
			$start = date('Y-m-d 00:00:00', strtotime('monday this week'));
		}
		if ($end === null) {
			$end = date('Y-m-d 23:59:59', strtotime('sunday this week'));
		}
	}

	public function actionUpdate(string $id, string $start_at): Response {
		$model = Reminder::findOne($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		try {
			$date = (new DateTime($start_at))->format(DATE_ATOM);
			$model->updateAttributes([
				'date_at' => $date,
			]);
			return $this->asJson([
				'success' => true,
			]);
		} catch (Exception $e) {
			return $this->asJson([
				'success' => false,
				'message' => 'Invalid Start Date format',
			]);
		}
	}
}
