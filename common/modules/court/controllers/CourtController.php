<?php

namespace common\modules\court\controllers;

use common\modules\court\models\Court;
use common\modules\court\models\search\CourtSearch;
use common\modules\court\Module;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CourtController implements the CRUD actions for Court model.
 *
 * @property Module $module
 */
class CourtController extends Controller {

	/**
	 * @inheritDoc
	 */
	public function behaviors(): array {
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::class,
					'actions' => [
						'delete' => ['POST'],
					],
				],
			]
		);
	}

	/**
	 * Lists all Court models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$searchModel = new CourtSearch();
		$dataProvider = $searchModel->search($this->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Creates a new Court model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate() {
		$model = new Court();

		if ($this->request->isPost) {
			if ($model->load($this->request->post()) && $model->save()) {
				return $this->redirect(['view', 'id' => $model->id]);
			}
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Displays a single Court model.
	 *
	 * @param int $id ID
	 * @return string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		$model = $this->findModel($id);
		$query = $model->getLawsuits()
			->with('issues')
			->with('issues.customer.userProfile')
			->orderBy(['due_at' => SORT_ASC]);
		if ($this->module->onlyUserIssues) {
			$query->usersIssues([Yii::$app->user->identity->getId()]);
		}
		$lawsuitsDataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		return $this->render('view', [
			'model' => $this->findModel($id),
			'lawsuitsDataProvider' => $lawsuitsDataProvider,
		]);
	}

	/**
	 * Updates an existing Court model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id ID
	 * @return string|Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = $this->findModel($id);

		if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Finds the Court model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id ID
	 * @return Court the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): Court {
		if (($model = Court::findOne(['id' => $id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('court', 'The requested page does not exist.'));
	}
}
