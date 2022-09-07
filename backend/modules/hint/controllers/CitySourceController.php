<?php

namespace backend\modules\hint\controllers;

use Yii;
use common\models\hint\HintCitySource;
use common\models\hint\searches\HintCitySourceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CitySourceController implements the CRUD actions for HintCitySource model.
 */
class CitySourceController extends Controller {

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
	 * Lists all HintCitySource models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new HintCitySourceSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single HintCitySource model.
	 *
	 * @param integer $source_id
	 * @param integer $hint_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($source_id, $hint_id) {
		return $this->render('view', [
			'model' => $this->findModel($source_id, $hint_id),
		]);
	}

	/**
	 * Updates an existing HintCitySource model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $source_id
	 * @param integer $hint_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($source_id, $hint_id) {
		$model = $this->findModel($source_id, $hint_id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'source_id' => $model->source_id, 'hint_id' => $model->hint_id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing HintCitySource model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $source_id
	 * @param integer $hint_id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($source_id, $hint_id) {
		$this->findModel($source_id, $hint_id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the HintCitySource model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $source_id
	 * @param integer $hint_id
	 * @return HintCitySource the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($source_id, $hint_id): HintCitySource {
		if (($model = HintCitySource::findOne(['source_id' => $source_id, 'hint_id' => $hint_id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('hint', 'The requested page does not exist.'));
	}
}
