<?php

namespace frontend\controllers;

use backend\widgets\CsvForm;
use common\models\issue\IssueMeet;
use common\models\user\Worker;
use frontend\models\AgentMeetSearch;
use frontend\models\meet\MeetForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii2tech\csvgrid\CsvGrid;

class MeetController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::ROLE_MEET],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	public function actionIndex() {
		$user = Yii::$app->user;
		$searchModel = new AgentMeetSearch();
		if ($user->can(Worker::ROLE_ARCHIVE)) {
			$searchModel->withArchive = true;
		}
		$searchModel->agent_id = $user->id;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if (isset($_POST[CsvForm::BUTTON_NAME])) {
			$exporter = new CsvGrid([
				'query' => $dataProvider->query,
				'columns' => [
					[
						'attribute' => 'phone',
						'label' => 'Telefon',
					],
				],
			]);
			return $exporter->export()->send('export.csv');
		}

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreate(string $date = null) {
		$model = new MeetForm();
		$model->agentId = Yii::$app->user->getId();
		if ($date === null) {
			$date = date(DATE_ATOM);
		} else {
			$date = date(DATE_ATOM, strtotime($date));
		}
		$model->dateStart = $date;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getId()]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionUpdate(int $id) {
		$model = new MeetForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getId()]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	public function actionDelete(int $id) {
		$model = $this->findModel($id);
		if (!$model->hasCampaign()) {
			$model->delete();
		}
		return $this->redirect('index');
	}

	/**
	 * @param int $id
	 * @return IssueMeet
	 * @throws MethodNotAllowedHttpException
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id): IssueMeet {
		$user = Yii::$app->user;

		$query = IssueMeet::find()
			->andWhere(['id' => $id]);
		if (!$user->can(Worker::ROLE_MANAGER)) {
			$query->andWhere(['agent_id' => $user->id]);
		}
		$model = $query->one();

		if ($model === null) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}

		if ($model->isArchived() && !$user->can(Worker::ROLE_ARCHIVE)) {
			Yii::warning('User: ' . $user->id . ' try view archived meet: ' . $model->id, 'meet');
			throw new MethodNotAllowedHttpException('The requested page does not exist.');
		}

		return $model;
	}

}
