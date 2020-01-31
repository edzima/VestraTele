<?php

namespace frontend\controllers;

use common\models\issue\IssueMeet;
use common\models\User;
use frontend\models\AgentMeetSearch;
use frontend\models\TeleMeetSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class MeetController extends Controller {

	public function actionIndex() {
		$user = Yii::$app->user;
		if ($user->can(User::ROLE_AGENT)) {
			return $this->redirect('agent');
		}
		if ($user->can(User::ROLE_TELEMARKETER)) {
			return $this->redirect('tele');
		}

		throw new NotFoundHttpException();
	}

	public function actionTele(): string {
		$user = Yii::$app->user;
		if (!$user->can(User::ROLE_TELEMARKETER)) {
			throw new NotFoundHttpException();
		}
		$searchModel = new TeleMeetSearch();
		$searchModel->tele_id = $user->id;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionAgent(): string {
		$user = Yii::$app->user;
		if (!$user->can(User::ROLE_AGENT)) {
			throw new NotFoundHttpException();
		}
		$searchModel = new AgentMeetSearch();
		$searchModel->agent_id = $user->id;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreate() {
		$user = Yii::$app->user;

		if (!$user->can(User::ROLE_TELEMARKETER)) {
			throw new NotFoundHttpException();
		}
		$model = new IssueMeet();
		$model->tele_id = $user->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionUpdate(int $id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionView($id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	protected function findModel(int $id): IssueMeet {
		$user = Yii::$app->user;

		$model = IssueMeet::find()
			->andWhere(['id' => $id])
			->andWhere([
				'or',
				['tele_id' => $user->getId()],
				['agent_id' => $user->getId()],
			])
			->one();
		if ($model === null) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}

		return $model;
	}

}
