<?php

namespace common\modules\calendar\controllers;

use common\helpers\ArrayHelper;
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
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}

		$model = new LeadReminderSearch();
		$model->onlyDelayed = null;
		$model->user_id = Yii::$app->user->getId();
		$model->dateStart = $start;
		$model->dateEnd = $end;

		return $this->asJson($model->getEventsData());
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

		return $this->asJson([
			'success' => false,
			'errors' => $model->getErrors(),
		]);
	}
}
