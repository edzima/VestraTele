<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\CalendarNews;
use common\models\Task;
use common\models\Wojewodztwa;
use common\models\AccidentTyp;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use common\models\TaskEvent;
use common\models\NewsEvent;

/**
 * Class CalendarController
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 * @todo to remove after move to new meet calendar.
 */
class CalendarController extends Controller {

	public function behaviors(): array {
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

	public function actionAgent(int $id) {

		if (Yii::$app->user->getId() !== $id
			&& !Yii::$app->user->can(User::ROLE_AGENT)) {
			throw new NotFoundHttpException('Brak uprawnień ;)');
		}

		$model = new Task();
		$model->agent_id = $id;
		$model->tele_id = $id;
		$woj = ArrayHelper::map(Wojewodztwa::find()->all(), 'id', 'name');
		$accident = ArrayHelper::map(AccidentTyp::find()->all(), 'id', 'name');

		$agent = ArrayHelper::map(User::find()->where(['typ_work' => 'P'])->all(), 'id', 'username');

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['/task/view', 'id' => $model->id]);
		} else {
			return $this->render('agent', [
				'model' => $model,
				'woj' => $woj,
				'accident' => $accident,
				'agent' => $agent,

			]);
		}
	}

	public function actionView($id) {
		//block for agent who isnt manager
		if (!Yii::$app->user->can('telemarketer')) {
			throw new NotFoundHttpException('Brak uprawnień ;)');
		}
		$model = new Task();
		$model->agent_id = $id;
		$model->tele_id = Yii::$app->user->id;
		$woj = ArrayHelper::map(Wojewodztwa::find()->all(), 'id', 'name');
		$accident = ArrayHelper::map(AccidentTyp::find()->all(), 'id', 'name');

		$agent = ArrayHelper::map(User::find()->where(['typ_work' => 'P'])->all(), 'id', 'username');

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['/task/view', 'id' => $model->id]);
		} else {
			return $this->render('view', [
				'model' => $model,
				'woj' => $woj,
				'accident' => $accident,
				'agent' => $agent,
			]);
		}

		// $agent = ArrayHelper::map(User::find()->where(['typ_work' => 'P'])->all(), 'id', 'username');
		//  return $this->render('view', ['agent' => $agent,'id' => $id]);
	}

	public function actionAgenttask($id, $start, $end) {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$model = Task::agentTask($id, $start, $end);
		$events = [];
		foreach ($model as $task) {
			$event = new TaskEvent($task);
			$event->updateURL();
			$events[] = $event->toArray();
		}
		return $events;
	}

	/**
	 * @param $id agentID task
	 * @param $start search
	 * @param $end search
	 * @return array JSON events FullCalendar
	 */
	public function actionOneagent($id, $start, $end) {

		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$model = Task::agentTask($id, $start, $end);
		$events = [];
		foreach ($model as $task) {
			$event = new TaskEvent($task);
			//set url as Raport Task
			$event->raportURL();
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

	public function actionRemove() {
		$id = Yii::$app->request->post()['event_id'];
		$this->delete($id);
		echo $id;
	}

	public function actionUpdate($id, $start) {
		$model = $this->findTask($id);
		$model->date = $start;
		//$model->end = $end;
		if ($model->save()) {
			return true;
		} else {
			return false;
		}
	}

	public function actionUpdatenews($id, $start, $end) {
		$model = $this->findModel($id);
		$model->start = $start;
		$model->end = $end;
		if ($model->save()) {
			return true;
		} else {
			return false;
		}
	}

	protected function delete($id) {
		$this->findModel($id)->delete();
	}

	/**
	 * Finds the Task model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Task the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */

	protected function findTask($id) {
		if (($model = Task::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	/**
	 * Finds the Task model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return CalendarNews the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = CalendarNews::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

}
