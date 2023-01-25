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

	public function actionIndex(int $userId = null, int $parentTypeId = null): string {
		$users = null;
		if ($userId === null) {
			$userId = Yii::$app->user->getId();
		}
		$searchModel = new ContactorSummonCalendarSearch();
		$searchModel->issueParentTypeId = $parentTypeId;
		$searchModel->contractor_id = $this->ensureUserId($userId);
		if (Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
			$users = $searchModel->getContractorsNames();
		} elseif (Yii::$app->user->can(Worker::PERMISSION_SUMMON_CREATE)) {
			$users = $searchModel->getContractorsNames(Yii::$app->user->id);
		}
		return $this->render('index', [
			'users' => $users,
			'user_id' => $userId,
			'indexUrl' => Url::to($this->summonIndexRoute),
			'searchModel' => $searchModel,
		]);
	}

	public function actionList(string $start = null, string $end = null, int $userId = null, int $parentTypeId = null): Response {
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}

		$model = new ContactorSummonCalendarSearch();
		$model->issueParentTypeId = $parentTypeId;
		$model->contractor_id = $this->ensureUserId($userId);
		$model->start = $start;
		$model->end = $end;

		return $this->asJson($model->getEventsData([
			'urlRoute' => $this->summonViewRoute,
		]));
	}

	public function actionDeadline(string $start = null, string $end = null, int $userId = null, int $parentTypeId = null): Response {
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}

		$model = new ContactorSummonCalendarSearch();
		$model->scenario = ContactorSummonCalendarSearch::SCENARIO_DEADLINE;
		$model->issueParentTypeId = $parentTypeId;
		$model->contractor_id = $this->ensureUserId($userId);
		$model->start = $start;
		$model->end = $end;

		return $this->asJson($model->getEventsData([
			'urlRoute' => $this->summonViewRoute,
		]));
	}

	protected function ensureUserId(int $userId = null): int {
		if ($userId === null) {
			$userId = Yii::$app->user->getId();
		}
		if (Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
			return $userId;
		}
		if ($userId !== Yii::$app->user->getId()) {
			if (!Yii::$app->user->can(Worker::PERMISSION_SUMMON_CREATE)
				|| !isset(ContactorSummonCalendarSearch::getSelfContractorsNames(Yii::$app->user->getId())[$userId])) {
				$userId = Yii::$app->user->getId();
			}
		}
		return $userId;
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
