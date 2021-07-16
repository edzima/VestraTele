<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\LeadQuestion;
use Yii;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\searches\LeadReportSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ReportController implements the CRUD actions for LeadReport model.
 */
class ReportController extends BaseController {

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
	 * Lists all LeadReport models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new LeadReportSearch();
		if ($this->module->onlyUser) {
			$userId = Yii::$app->user->getId();
			if ($userId === null) {
				throw new NotFoundHttpException();
			}
			$searchModel->scenario = LeadReportSearch::SCENARIO_OWNER;
			$searchModel->owner_id = $userId;
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'visibleButtons' => [
				'delete' => $this->module->allowDelete,
			],
		]);
	}

	/**
	 * Displays a single LeadReport model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
			'withDelete' => $this->module->allowDelete,
		]);
	}

	public function actionReport(int $id, int $status_id = null) {
		$model = new ReportForm();
		$model->owner_id = (int) Yii::$app->user->getId();
		$model->setLead($this->findLead($id));
		if ($status_id) {
			$model->status_id = $status_id;
		}
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['lead/view', 'id' => $id]);
		}

		return $this->render('report', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing LeadReport model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new ReportForm();
		$model->setModel($this->findModel($id));
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['lead/view', 'id' => $model->getModel()->lead_id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing LeadReport model.
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

	public function actionSchema(): array {
		Yii::$app->response->format = Response::FORMAT_JSON;
		$out = [];
		if (isset($_POST['depdrop_parents'])) {
			$status_id = reset($_POST['depdrop_parents']);
			if (isset($_POST['depdrop_params'])) {
				$type_id = reset($_POST['depdrop_params']);
			}
			$schemas = LeadQuestion::findWithStatusAndType($status_id, $type_id);
			foreach ($schemas as $schema) {
				$out[$schema->id] = ['id' => $schema->id, 'name' => $schema->name];
			}
		}
		return ['output' => $out, 'selected' => array_key_first($out)];
	}

	/**
	 * Finds the LeadReport model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return LeadReport the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): LeadReport {
		$model = LeadReport::findOne($id);
		if ($model === null || !$this->isForUser($model)) {
			throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
		}
		return $model;
	}

	private function isForUser(LeadReport $model): bool {
		if (!$this->module->onlyUser) {
			return true;
		}
		return $model->owner_id === Yii::$app->user->getId();
	}
}
