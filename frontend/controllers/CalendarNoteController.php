<?php

namespace frontend\controllers;

use common\models\CalendarNews;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class CalendarNoteController extends Controller {

	public function behaviors() {
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
		parent::runAction($id, $params);
	}

	public function actionList(string $dateFrom = null, string $dateTo = null, int $agentId = null): Response {
		if ($agentId === null) {
			$agentId = Yii::$app->user->getId();
		}
		if ($dateFrom === null) {
			$dateFrom = date('Y-m-01');
		}
		if ($dateTo === null) {
			$dateTo = date('Y-m-t 23:59:59');
		}

		$models = CalendarNews::find()
			->andWhere(['agent_id' => $agentId])
			->andWhere(['>=', 'start', $dateFrom])
			->andWhere(['<=', 'end', $dateTo])
			->all();
		$data = [];
		foreach ($models as $model) {
			$data[] = [
				'id' => $model->id,
				'title' => $model->news,
				'start' => $model->start,
				'end' => $model->end,
			];
		}

		return $this->asJson($data);
	}

	public function actionAdd(int $agent_id, string $news, string $date): Response {
		$model = new CalendarNews();
		$model->agent_id = $agent_id;
		$model->news = $news;
		$model->start = $date;
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
