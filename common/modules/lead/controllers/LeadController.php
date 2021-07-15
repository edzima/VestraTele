<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\searches\LeadSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadController implements the CRUD actions for Lead model.
 */
class LeadController extends BaseController {

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
	public function actionIndex() {
		if (Yii::$app->request->post('selection')) {
			return $this->redirect(['user/assign', 'ids' => Yii::$app->request->post('selection')]);
		}
		$searchModel = new LeadSearch();
		if ($this->module->onlyUser) {
			if (Yii::$app->user->getIsGuest()) {
				return Yii::$app->user->loginRequired();
			}
			$searchModel->setScenario(LeadSearch::SCENARIO_USER);
			$searchModel->user_id = Yii::$app->user->getId();
		}

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'assignUsers' => !$this->module->onlyUser,
			'visibleButtons' => [
				'delete' => $this->module->allowDelete,
			],
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
			'model' => $this->findLead($id),
		]);
	}

	/**
	 * Creates a new Lead model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new LeadForm();
		$model->date_at = date($model->dateFormat);
		if ($this->module->onlyUser) {
			$model->setScenario(LeadForm::SCENARIO_OWNER);
			$model->owner_id = Yii::$app->user->getId();
		}
		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$lead = $this->module->manager->pushLead($model);
			if ($lead) {
				Yii::$app->session->addFlash('success', Yii::t('lead', 'Success create Lead.'));
				return $this->redirect(['view', 'id' => $lead->getId()]);
			}
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
		$lead = $this->findLead($id);
		$model->setLead($lead);

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$lead->setLead($model);
			$lead->update();
			return $this->redirect(['view', 'id' => $lead->getId()]);
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
		$this->findLead($id)->delete();

		return $this->redirect(['index']);
	}

}
