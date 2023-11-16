<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\search\ShipmentPocztaPolskaSearch;
use common\models\issue\IssueShipmentPocztaPolska;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ShipmentPocztaPolskaController implements the CRUD actions for IssueShipmentPocztaPolska model.
 */
class ShipmentPocztaPolskaController extends Controller {

	/**
	 * @inheritDoc
	 */
	public function behaviors() {
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
	 * Lists all IssueShipmentPocztaPolska models.
	 *
	 * @return string
	 */
	public function actionIndex() {
		$searchModel = new ShipmentPocztaPolskaSearch();
		$dataProvider = $searchModel->search($this->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssueShipmentPocztaPolska model.
	 *
	 * @param int $issue_id Issue ID
	 * @param string $shipment_number Shipment Number
	 * @return string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $issue_id, string $shipment_number) {
		return $this->render('view', [
			'model' => $this->findModel($issue_id, $shipment_number),
		]);
	}

	/**
	 * Creates a new IssueShipmentPocztaPolska model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate() {
		$model = new IssueShipmentPocztaPolska();

		if ($this->request->isPost) {
			if ($model->load($this->request->post()) && $model->save()) {
				return $this->redirect(['view', 'issue_id' => $model->issue_id, 'shipment_number' => $model->shipment_number]);
			}
		} else {
			$model->loadDefaultValues();
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueShipmentPocztaPolska model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $issue_id Issue ID
	 * @param string $shipment_number Shipment Number
	 * @return string|Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $issue_id, string $shipment_number) {
		$model = $this->findModel($issue_id, $shipment_number);

		if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['view', 'issue_id' => $model->issue_id, 'shipment_number' => $model->shipment_number]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueShipmentPocztaPolska model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $issue_id Issue ID
	 * @param string $shipment_number Shipment Number
	 * @return Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $issue_id, string $shipment_number) {
		$this->findModel($issue_id, $shipment_number)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the IssueShipmentPocztaPolska model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $issue_id Issue ID
	 * @param string $shipment_number Shipment Number
	 * @return IssueShipmentPocztaPolska the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $issue_id, string $shipment_number) {
		if (($model = IssueShipmentPocztaPolska::findOne(['issue_id' => $issue_id, 'shipment_number' => $shipment_number])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('issue', 'The requested page does not exist.'));
	}
}
