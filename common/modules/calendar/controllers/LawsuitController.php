<?php

namespace common\modules\calendar\controllers;

use common\helpers\ArrayHelper;
use common\modules\calendar\models\searches\LawsuitCalendarSearch;
use common\modules\calendar\models\searches\LawsuitSummonCalendarSearch;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class LawsuitController extends Controller {

	public string $summonViewRoute = '/issue/summon/view';
	public string $defaultStartDateFormat = 'Y-m-01';
	public string $defaultEndDateFormat = 'Y-m-t 23:59:59';

	public function runAction($id, $params = []) {
		if ($id !== 'index') {
			$params = ArrayHelper::merge(Yii::$app->getRequest()->getBodyParams(), $params);
		}
		return parent::runAction($id, $params);
	}

	public function actionIndex(): string {
		$searchModel = new LawsuitCalendarSearch();
		if (YII_IS_FRONTEND) {
			$searchModel->issueUserIds = [
				Yii::$app->user->getId(),
			];
		}
		$summonsModel = new LawsuitSummonCalendarSearch();
		return $this->render('index', [
			'searchModel' => $searchModel,
			'summonsModel' => $summonsModel,
		]);
	}

	public function actionList(string $start = null, string $end = null): Response {
		if ($start === null) {
			$start = date($this->defaultStartDateFormat);
		}
		if ($end === null) {
			$end = date($this->defaultEndDateFormat);
		}

		$model = new LawsuitCalendarSearch();
		if (YII_IS_FRONTEND) {
			$model->issueUserIds = [
				Yii::$app->user->getId(),
			];
		}
		$model->startAt = $start;
		$model->endAt = $end;

		return $this->asJson($model->getEventsData());
	}

	public function actionSummonsList(string $start = null, string $end = null): Response {
		if ($start === null) {
			$start = date($this->defaultStartDateFormat);
		}
		if ($end === null) {
			$end = date($this->defaultEndDateFormat);
		}
		$model = new LawsuitSummonCalendarSearch();
		$model->start = $start;
		$model->end = $end;
		if (YII_IS_FRONTEND) {
			$model->contractor_id = Yii::$app->user->getId();
		}
		return $this->asJson($model->getEventsData([
			'urlRoute' => $this->summonViewRoute,
		]));
	}
}
