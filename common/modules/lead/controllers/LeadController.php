<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\forms\LeadForm;
use Yii;
use common\modules\lead\models\Lead;
use common\modules\lead\models\searches\LeadSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadController implements the CRUD actions for Lead model.
 */
class LeadController extends Controller {

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

	/**
	 * Lists all Lead models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new LeadSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Lead model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new Lead model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new LeadForm(['datetime' => time()]);
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$lead = Yii::$app->leadManager->pushLead($model);
			return $this->redirect(['view', 'id' => $lead->getId()]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Lead model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new LeadForm();
		$lead = $this->findModel($id);
		$model->setLead($lead);

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$lead->setLead($model);
			$lead->update();
			return $this->redirect(['view', 'id' => $lead->id]);
		}

		return $this->render('update', [
			'id' => $id,
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Lead model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the Lead model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Lead the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): ActiveLead {
		$model = Yii::$app->leadManager->findById($id);
		if ($model !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}
