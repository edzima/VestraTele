<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\CalendarNews;
use common\models\Task;

use common\models\TaskEvent;

use common\models\NewsEvent;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class CalendarController extends Controller {

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'addnews' => ['POST'],
				],
			],
		];
	}

	public function actionLayer($id = null) {
		$layers = Yii::$app->authManager->getUserIdsByRole(User::ROLE_LAYER);
		if ($layers) {
			if (in_array($id, $layers)) {
				$layer = ArrayHelper::map(User::find()->where(['id' => $layers])->all(), 'id', 'username');
			} else {
				$id = $layers[0];
				return $this->redirect(['layer', 'id' => $id]);
			}

			return $this->render('layer', ['layer' => $layer, 'id' => $id]);
		}
		throw new NotFoundHttpException('Layer does not exist.');
	}

	public function actionAgent($id = null) {

		$agents = Yii::$app->authManager->getUserIdsByRole(User::ROLE_AGENT);
		if ($agents) {
			if (in_array($id, $agents)) {
				$agent = ArrayHelper::map(User::find()->where(['id' => $agents])->all(), 'id', 'username');
			} else {
				$id = $agents[0];
				$agent = ArrayHelper::map(User::find()->where(['id' => $id])->all(), 'id', 'username');
				return $this->redirect(['agent', 'id' => $id]);
			}
			return $this->render('view', ['agent' => $agent, 'id' => $id]);
		}
		throw new NotFoundHttpException('Agent does not exist.');
	}

	// $id -> agent_id news
	public function actionAddnews() {
		$agent = Yii::$app->request->post()['agent'];
		$start = Yii::$app->request->post()['start'];
		$end = Yii::$app->request->post()['end'];
		$newsText = Yii::$app->request->post()['newsText'];

		$model = new Calendarnews();
		$model->agent_id = $agent;
		$model->news = $newsText;
		$model->start = $start;
		$model->end = $end;
		$model->save();
		echo $model->getPrimaryKey();
	}

	public function actionAgenttask($id, $start, $end) {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$model = Task::agentTask($id, $start, $end);
		$events = [];
		foreach ($model as $task) {
			$event = new TaskEvent($task);
			$event->backendRaportURL();
			$events[] = $event->toArray();
		}
		return $events;
	}

	public function actionAgentnews($id, $start, $end) {

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$model = CalendarNews::find()
			->where(['agent_id' => $id])
			->andWhere("start BETWEEN '$start' AND '$end'")
			->all();
		$events = [];
		foreach ($model as $calendarNews) {
			$event = new NewsEvent($calendarNews);
			$events[] = $event->toArray();
		}
		return $events;
	}

	public function actionRemove() {
		$id = Yii::$app->request->post()['event_id'];
		$this->delete($id);
		echo $id;
	}

	/**
	 * @todo maybe to remove
	 * @param $id
	 * @param $start
	 * @return bool
	 */
	public function actionUpdate($id, $start) {
		$model = $this->findTask($id);
		$model->date = $start;
		if ($model->save()) {
			return true;
		}
		return false;
	}

	public function actionUpdatenews($id, $start, $end) {
		$model = $this->findModel($id);
		$model->start = $start;
		$model->end = $end;
		if ($model->save()) {
			return true;
		}
		return false;
	}

	private function delete($id) {
		$this->findModel($id)->delete();
	}

	/**
	 * Finds the Task model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return CalendarNews the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */

	protected function findModel($id): CalendarNews {
		if (($model = CalendarNews::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
