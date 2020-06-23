<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\MeetForm;
use backend\widgets\CsvForm;
use common\models\User;
use Yii;
use common\models\issue\IssueMeet;
use common\models\issue\IssueMeetSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2tech\csvgrid\CsvGrid;

/**
 * MeetController implements the CRUD actions for IssueMeet model.
 */
class MeetController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],

		];
	}

	public function beforeAction($action) {
		$before = parent::beforeAction($action);
		if ($before && Yii::$app->user->can(User::ROLE_MEET)) {
			return true;
		}
		throw new ForbiddenHttpException();
	}

	/**
	 * Lists all IssueMeet models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new IssueMeetSearch();
		if (Yii::$app->user->can(User::ROLE_ARCHIVE)) {
			$searchModel->withArchive = true;
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		if (isset($_POST[CsvForm::BUTTON_NAME])) {
			$exporter = new CsvGrid([
				'query' => $dataProvider->query,
				'columns' => [
					[
						'attribute' => 'clientFullName',
						'label' => 'Nazwa',
					],
					[
						'attribute' => 'street',
					],
					['attribute' => 'phone'],
					[
						'attribute' => 'city.name',
						'label' => 'Miasto',
					],
					[
						'attribute' => 'province.name',
						'label' => 'Powiat',
					],
					[
						'attribute' => 'state.name',
						'label' => 'Województwo',
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

	/**
	 * Displays a single IssueMeet model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new IssueMeet model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new MeetForm();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueMeet model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id) {

		$model = new MeetForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueMeet model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the IssueMeet model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueMeet the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id):IssueMeet {
		if (($model = IssueMeet::findOne($id)) !== null) {
			if ($model->isArchived() && !Yii::$app->user->can(User::ROLE_ARCHIVE)) {
				Yii::warning('User: ' . Yii::$app->user->id . ' try view archived meet: ' . $model->id, 'meet');

				throw new MethodNotAllowedHttpException('Spotkanie jest w archiwum.');
			}
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
