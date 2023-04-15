<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\models\forms\LeadSourceChangeForm;
use common\modules\lead\models\forms\LeadSourceForm;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\searches\LeadSourceSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SourceController implements the CRUD actions for LeadSource model.
 */
class SourceController extends BaseController {

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

	public function actionChange(array $ids = []) {
		if (empty($ids)) {
			$postIds = Yii::$app->request->post('leadsIds');
			if (is_string($postIds)) {
				$postIds = explode(',', $postIds);
			}
			if ($postIds) {
				$ids = $postIds;
			}
		}
		if (empty($ids)) {
			Flash::add(Flash::TYPE_WARNING, 'Ids cannot be blank.');
			return $this->redirect(['lead/index']);
		}
		$ids = array_unique($ids);
		$model = new LeadSourceChangeForm();
		$model->ids = $ids;
		if ($model->load(Yii::$app->request->post())) {
			$count = $model->save();
			if ($count) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('lead', 'Success Change Source: {source} for Leads: {count}.', [
						'source' => $this->findModel($model->source_id)->name,
						'count' => $count,
					]));
				return $this->redirect(['lead/index']);
			}
		}
		return $this->render('change', [
			'model' => $model,
		]);
	}

	/**
	 * Lists all LeadSource models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new LeadSourceSearch();

		if ($this->module->onlyUser) {
			if (Yii::$app->user->getIsGuest()) {
				return Yii::$app->user->loginRequired();
			}
			$searchModel->setScenario(LeadSourceSearch::SCENARIO_OWNER);
			$searchModel->owner_id = Yii::$app->user->getId();
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
	 * Displays a single LeadSource model.
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
	 * Creates a new LeadSource model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new LeadSourceForm();

		if ($this->module->onlyUser) {
			$model->setScenario(LeadSourceForm::SCENARIO_OWNER);
			$model->owner_id = Yii::$app->user->getId();
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing LeadSource model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new LeadSourceForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing LeadSource model.
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
	 * Finds the LeadSource model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return LeadSource the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): LeadSource {
		$model = LeadSource::findOne($id);
		if ($model !== null) {
			if (!$this->module->onlyUser
				|| ($model->owner_id !== null && $model->owner_id === (int) Yii::$app->user->getId())) {
				return $model;
			}
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}
