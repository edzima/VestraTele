<?php

namespace backend\modules\hint\controllers;

use backend\modules\hint\models\HintCityForm;
use backend\modules\hint\models\HintDistrictForm;
use Yii;
use common\models\hint\HintCity;
use common\models\hint\searches\HintCitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CityController implements the CRUD actions for HintCity model.
 */
class CityController extends Controller {

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
	 * Lists all HintCity models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new HintCitySearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single HintCity model.
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
	 * Creates a new HintCity model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new HintCityForm();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Creates a new HintCity models for District.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreateDistrict() {
		$model = new HintDistrictForm();

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$count = $model->save(false);
			Yii::$app->session->addFlash('success',
				Yii::t('hint', 'Success generate for {count} cities.', ['count' => $count])
			);
			return $this->redirect(['index']);
		}

		return $this->render('create-district', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing HintCity model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new HintCityForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing HintCity model.
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
	 * Finds the HintCity model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return HintCity the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): HintCity {
		if (($model = HintCity::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('hint', 'The requested page does not exist.'));
	}
}
