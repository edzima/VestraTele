<?php

namespace backend\modules\provision\controllers;

use backend\helpers\Url;
use backend\modules\provision\models\ProvisionUpdateForm;
use backend\modules\provision\models\search\ProvisionDoubleSearch;
use common\models\provision\Provision;
use common\models\provision\ProvisionSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProvisionController implements the CRUD actions for Provision model.
 */
class ProvisionController extends Controller {

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

	public function actionDoubles(): string {
		$searchModel = new ProvisionDoubleSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('doubles', [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}

	/**
	 * Lists all Provision models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new ProvisionSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		Url::remember();
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Updates an existing Provision model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new ProvisionUpdateForm($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->goBack();
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Provision model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$this->findModel($id)->delete();

		return $this->redirect(Url::previous());
	}

	/**
	 * Finds the Provision model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Provision the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): Provision {
		if (($model = Provision::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
