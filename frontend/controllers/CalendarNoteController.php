<?php

namespace frontend\controllers;

use common\models\CalendarNews;
use common\models\user\User;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;

class CalendarNoteController extends Controller {

	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'add' => ['POST'],
					'update' => ['POST'],
					'delete' => ['POST'],
				],
			],
		];
	}

	public function runAction($id, $params = []) {
		$params = array_merge($_POST, $params);
		return parent::runAction($id, $params);
	}

	public function actionList(string $start = null, string $end = null, int $agentId = null): Response {
		if ($agentId === null) {
			$agentId = (int) Yii::$app->user->getId();
		}
		if ($agentId !== (int) Yii::$app->user->getId() && !Yii::$app->user->can(User::ROLE_MANAGER)) {
			throw new MethodNotAllowedHttpException();
		}
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}
		/** @var CalendarNews[] $models */
		$models = CalendarNews::find()
			->andWhere(['agent_id' => $agentId])
			->andWhere(['>=', 'start_at', $start])
			->andWhere(['<=', 'end_at', $end])
			->all();
		$data = [];
		foreach ($models as $model) {
			$data[] = [
				'id' => $model->id,
				'title' => $model->text,
				'start' => $model->start_at,
				'end' => $model->end_at,
			];
		}

		return $this->asJson($data);
	}

	public function actionAdd(int $agent_id, string $news, string $date): Response {
		$model = new CalendarNews();
		$model->user_id = $agent_id;
		$model->text = $news;
		$model->start_at = $date;
		if ($model->save()) {
			return $this->asJson([
				'id' => $model->id,
			]);
		}
		return $this->asJson(['errors' => $model->getErrors()]);
	}

	public function actionUpdate(int $id): Response {
		$model = $this->findModel($id);
		if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
			return $this->asJson(['success' => true]);
		}
		return $this->asJson([
			'errors' => $model->getErrors(),
		]);
	}

	public function actionDelete(int $id): Response {
		$model = $this->findModel($id);
		return $this->asJson([
			'success' => $model->delete(),
		]);
	}

	private function findModel(int $id): CalendarNews {
		$model = CalendarNews::findOne($id);
		if ($model !== null) {
			return $model;
		}
		throw new ForbiddenHttpException();
	}

}
