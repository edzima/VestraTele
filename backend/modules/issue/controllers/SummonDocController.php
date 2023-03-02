<?php

namespace backend\modules\issue\controllers;

use backend\helpers\Url;
use backend\modules\issue\models\search\SummonDocSearch;
use backend\modules\issue\models\SummonDocForm;
use common\models\issue\SummonDoc;
use common\models\issue\SummonDocLink;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * SummonDocController implements the CRUD actions for SummonDoc model.
 */
class SummonDocController extends Controller {

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

	public function actionTypesList() {
		Yii::$app->response->format = Response::FORMAT_JSON;
		$out = [];
		if (isset($_POST['depdrop_parents'])) {
			$parents = $_POST['depdrop_parents'];
			if ($parents != null) {
				$type_id = $parents[0];
				$names = SummonDoc::getNames($type_id);
				foreach ($names as $id => $name) {
					$out[] = [
						'id' => $id,
						'name' => $name,
					];
				}
				return ['output' => $out, 'selected' => ''];
			}
		}
		return ['output' => '', 'selected' => ''];
	}

	/**
	 * Lists all SummonDoc models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new SummonDocSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single SummonDoc model.
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
	 * Creates a new SummonDoc model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new SummonDocForm();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing SummonDoc model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new SummonDocForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing SummonDoc model.
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
	 * Finds the SummonDoc model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return SummonDoc the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): SummonDoc {
		if (($model = SummonDoc::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('backend', 'The requested page does not exist.'));
	}

	public function actionDone(int $docId, int $summonId, string $returnUrl = null) {
		$summonLink = SummonDocLink::find()
			->andWhere([
				'doc_type_id' => $docId,
				'summon_id' => $summonId,
			])
			->one();
		if ($summonLink === null) {
			throw new NotFoundHttpException();
		}
		$summonLink->done_at = date(DATE_ATOM);
		$summonLink->save();
		if ($returnUrl === null) {
			$returnUrl = Url::previous();
		}
		return $this->redirect($returnUrl);
	}
}
