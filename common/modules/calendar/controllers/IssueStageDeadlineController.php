<?php

namespace common\modules\calendar\controllers;

use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\models\issue\Issue;
use common\modules\calendar\models\searches\IssueStageDeadlineCalendarSearch;
use DateTime;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class IssueStageDeadlineController extends Controller {

	public string $defaultStartDateFormat = 'Y-m-01';
	public string $defaultEndDateFormat = 'Y-m-t 23:59:59';
	public string $issueIndexRoute = '/issue/issue/index';
	public string $issueUrlRoute = '/issue/issue/view';

	public function runAction($id, $params = []) {
		if ($id !== 'index') {
			$params = ArrayHelper::merge(Yii::$app->getRequest()->getBodyParams(), $params);
		}
		return parent::runAction($id, $params);
	}

	public function actionIndex(): string {
		return $this->render('index', [
			'indexUrl' => Url::to([$this->issueIndexRoute]),
		]);
	}

	public function actionList(string $start = null, string $end = null, int $userId = null): Response {
		if ($start === null) {
			$start = date($this->defaultStartDateFormat);
		}
		if ($end === null) {
			$end = date($this->defaultEndDateFormat);
		}

		$model = new IssueStageDeadlineCalendarSearch();
		$model->start = $start;
		$model->end = $end;

		return $this->asJson($model->getEventsData($this->issueUrlRoute));
	}

	public function actionUpdate(int $id, string $start_at): Response {
		$model = Issue::findOne($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		try {
			$date = (new DateTime($start_at))->format(DATE_ATOM);
			$model->updateAttributes([
				'stage_deadline_at' => $date,
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
