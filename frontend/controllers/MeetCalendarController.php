<?php

namespace frontend\controllers;

use common\models\issue\IssueMeet;
use common\models\user\Worker;
use frontend\models\AgentMeetCalendarSearch;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MeetCalendarController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::PERMISSION_MEET, Worker::ROLE_AGENT],
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
			$params = array_merge($_POST, $params);
		}
		return parent::runAction($id, $params);
	}

	public function actionIndex(int $agentId = null): string {
		if ($agentId === null) {
			$agentId = (int) Yii::$app->user->getId();
		}

		$agents = [];
		if (Yii::$app->user->can(Worker::ROLE_MANAGER)) {
			$agents = Worker::find()
				->with('userProfile')
				->leftJoin('issue_meet', 'user.id = issue_meet.agent_id')
				->where('issue_meet.agent_id IS NOT NULL')
				->all();

			$agents = ArrayHelper::map($agents, 'id', 'fullName');
		}

		return $this->render('index', [
			'agents' => $agents,
			'agentId' => $agentId,
		]);
	}

	public function actionList(string $start = null, string $end = null, int $agentId = null): Response {
		if ($agentId === null) {
			$agentId = Yii::$app->user->getId();
		}

		if ($agentId !== Yii::$app->user->getId() && !Yii::$app->user->can(Worker::ROLE_MANAGER)) {
			throw new MethodNotAllowedHttpException();
		}
		if ($start === null) {
			$start = date('Y - m - 01');
		}
		if ($end === null) {
			$end = date('Y - m - t 23:59:59');
		}
		$model = new AgentMeetCalendarSearch();
		$model->agent_id = $agentId;
		$model->start = $start;
		$model->end = $end;
		$data = [];

		foreach ($model->search()->getModels() as $model) {
			$data[] = [
				'id' => $model->id,
				'url' => Url::to(['meet/view', 'id' => $model->id]),
				'title' => $model->getClientFullName(),
				'start' => $model->date_at,
				'end' => $model->date_end_at,
				'phone' => Html::encode($this->getPhone($model)),
				'statusId' => $model->status,
				'tooltipContent' => $this->getTooltipContent($model),
			];
		}

		return $this->asJson($data);
	}

	private function getPhone(IssueMeet $model): string {
		$validator = new PhoneValidator();
		$validator->country = 'PL';
		if ($validator->validateAttribute($model, 'phone')) {
			return $model->phone;
		}
		return '';
	}

	private function getTooltipContent(IssueMeet $model): string {
		$tooltip = $model->details;
		$phone = $this->getPhone($model);
		if (!empty($phone)) {
			$tooltip .= ' Tel: ' . $phone;
		}
		return $tooltip;
	}

	public function actionUpdate(int $id, string $date_at, string $date_end_at): Response {
		$model = $this->findModel($id);
		$model->date_at = $date_at;
		$model->date_end_at = $date_end_at;
		if ($model->save()) {
			return $this->asJson(['success' => true]);
		}
		$response = $this->asJson([
			'success' => false,
			'errors' => $model->getErrors(),
		]);
		$response->statusCode = 400;
		return $response;
	}

	/**
	 * @param int $id
	 * @return IssueMeet
	 * @throws NotFoundHttpException
	 */
	private function findModel(int $id): IssueMeet {
		if (($model = IssueMeet::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException();
	}

}
