<?php

namespace common\modules\calendar\controllers;

use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\models\user\Worker;
use common\modules\calendar\models\searches\ContactorSummonCalendarSearch;
use common\modules\calendar\models\SummonCalendarEvent;
use DateTime;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SummonCalendarController extends Controller {

	public string $summonIndexRoute = '/summon/index';
	public string $summonCreateRoute = '/summon/create';
	public string $summonViewRoute = '/summon/view';

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'update' => ['POST'],
				],
			],
		];
	}

	public function runAction($id, $params = []) {
		if ($id !== 'index') {
			$params = ArrayHelper::merge(Yii::$app->getRequest()->getBodyParams(), $params);
		}
		return parent::runAction($id, $params);
	}

	public function actionIndex(int $userId = null): string {
		$users = null;
		if ($userId === null) {
			$userId = Yii::$app->user->getId();
		}
		if (Yii::$app->user->can(Worker::PERMISSION_SUMMON_CREATE)) {
			$users = ContactorSummonCalendarSearch::getSelfContractorsNames(Yii::$app->user->getId());
		}
		return $this->render('index', [
			'users' => $users,
			'user_id' => $userId,
			'indexUrl' => Url::to($this->summonIndexRoute),
			'createUrl' => Url::to($this->summonCreateRoute),
		]);
	}

	public function actionList(string $start = null, string $end = null, int $userId = null): Response {
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}
		if ($userId === null) {
			$userId = Yii::$app->user->getId();
		}
		$model = new ContactorSummonCalendarSearch();
		$model->contractor_id = $userId;
		$model->start = $start;
		$model->end = $end;

		return $this->asJson($model->getEventsData([
			'urlRoute' => $this->summonViewRoute,
		]));
	}

	public function actionDeadline(string $start = null, string $end = null, int $userId = null): Response {
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}
		if ($userId === null) {
			$userId = Yii::$app->user->getId();
		}
		$model = new ContactorSummonCalendarSearch();
		$model->scenario = ContactorSummonCalendarSearch::SCENARIO_DEADLINE;

		$model->contractor_id = $userId;
		$model->start = $start;
		$model->end = $end;

		return $this->asJson($model->getEventsData([
			'urlRoute' => $this->summonViewRoute,
		]));
	}

	public function actionUpdate(int $id, string $start_at): Response {
		$model = new SummonCalendarEvent();
		$model->id = $id;
		try {
			$start_at = (new DateTime($start_at))->format(DATE_ATOM);
		} catch (Exception $e) {
			return $this->asJson([
				'success' => false,
				'message' => 'Invalid Start Date format',
			]);
		}

		//@todo add check is for User

		$model->updateDate($start_at);
		return $this->asJson([
			'success' => false,
			'errors' => $model->getErrors(),
		]);
	}

}
