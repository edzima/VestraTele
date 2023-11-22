<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\search\ShipmentPocztaPolskaSearch;
use common\helpers\Flash;
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
	public function behaviors(): array {
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::class,
					'actions' => [
						'delete' => ['POST'],
						'refresh' => ['POST'],
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
	public function actionView(int $issue_id, string $shipment_number): string {
		return $this->render('view', [
			'model' => $this->findModel($issue_id, $shipment_number),
		]);
	}

	/**
	 * Refresh Shipment Info from  a single IssueShipmentPocztaPolska model.
	 *
	 * @param int $issue_id
	 * @param string $shipment_number
	 * @param string|null $returnUrl
	 * @return Response
	 * @throws NotFoundHttpException
	 */
	public function actionRefresh(int $issue_id, string $shipment_number, string $returnUrl = null) {
		$model = $this->findModel($issue_id, $shipment_number);
		$this->updateShipmentData($model);
		$returnUrl = $returnUrl ?: ['view', 'issue_id' => $issue_id, 'shipment_number' => $shipment_number];
		return $this->redirect($returnUrl);
	}

	protected function updateShipmentData(IssueShipmentPocztaPolska $model, bool $finishedForce = false): void {
		if (!$model->isFinished() || $finishedForce) {
			$poczta = Yii::$app->pocztaPolska;
			$poczta->checkShipment($model->shipment_number);
			$shipment = $poczta->getShipment();
			$model->setShipment($shipment);
			$model->save();
			if ($model->hasErrors()) {
				Yii::error($model->getErrors(), __METHOD__);
			}
			if ($shipment !== null) {
				if (!$shipment->isOk()) {
					Flash::add(
						Flash::TYPE_WARNING, $shipment->getStatusName()
					);
				}
			} else {
				Flash::add(
					Flash::TYPE_ERROR,
					Yii::t('issue', 'Problem with check Shipment Info: #{number}', [
						'number' => $model->shipment_number,
					])
				);
			}
		}
	}

	/**
	 * Creates a new IssueShipmentPocztaPolska model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate(int $issueId = null) {
		$model = new IssueShipmentPocztaPolska();
		$model->issue_id = $issueId;

		if ($this->request->isPost) {
			if ($model->load($this->request->post()) && $model->save()) {
				$this->updateShipmentData($model);
				return $this->redirect(['view', 'issue_id' => $model->issue_id, 'shipment_number' => $model->shipment_number]);
			}
		} else {
			$model->loadDefaultValues();
		}

		return $this->render('create', [
			'model' => $model,
			'issue' => $model->issue,
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
			if ($model->getShipment() && $model->getShipment()->numer !== $model->shipment_number) {
				$this->updateShipmentData($model, true);
			}
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
	protected function findModel(int $issue_id, string $shipment_number): IssueShipmentPocztaPolska {
		if (($model = IssueShipmentPocztaPolska::findOne(['issue_id' => $issue_id, 'shipment_number' => $shipment_number])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('issue', 'The requested page does not exist.'));
	}
}
