<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\PayCalculationForm;
use backend\modules\issue\models\search\NewPayCalculationSearch;
use common\models\issue\Issue;
use common\models\user\Worker;
use Yii;
use common\models\issue\IssuePayCalculation;
use backend\modules\issue\models\search\IssuePayCalculationSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PayCalculationController implements the CRUD actions for IssuePayCalculation model.
 */
class PayCalculationController extends Controller {

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
			'local-access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::ROLE_BOOKKEEPER],
					],
					[
						'allow' => true,
						'actions' => ['view'],
						'roles' => [Worker::PERMISSION_PAYS_DELAYED],
					],
				],
			],
		];
	}

	public function actionNew(): string {
		$searchModel = new NewPayCalculationSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('new', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all IssuePayCalculation models.
	 *
	 * @param int|null $issueId
	 * @return mixed
	 */
	public function actionIndex(int $issueId = null) {
		$searchModel = new IssuePayCalculationSearch();
		$searchModel->issue_id = $issueId;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssuePayCalculation model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView(int $id) {
		$model = $this->findModel($id);
		foreach ($model->pays as $pay) {
			if (empty($pay->provisions)) {
				Yii::$app->session->addFlash('warning', 'Brak ustawionych prowizji dla wpłaty: ' . Yii::$app->formatter->asDecimal($pay->value));
			}
		}

		return $this->render('view', [
			'model' => $model,
		]);
	}

	/**
	 * Create or Updates an existing IssuePayCalculation model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id Issue ID
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionCreate(int $id) {

		$model = new PayCalculationForm();
		$model->setIssue($this->findIssueModel($id));
		if ($model->load(Yii::$app->request->post())
			&& (!$model->isGenerate())
			&& $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssuePayCalculation model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new PayCalculationForm();
		$model->setModel($this->findModel($id));
		if ($model->load(Yii::$app->request->post())
			&& (!$model->isGenerate())
			&& $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssuePayCalculation model.
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
	 * Finds the IssuePayCalculation model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssuePayCalculation the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id): IssuePayCalculation {
		if (($model = IssuePayCalculation::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	/**
	 * @param int $id
	 * @return Issue
	 * @throws NotFoundHttpException
	 */
	protected function findIssueModel(int $id): Issue {
		if (($model = Issue::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
