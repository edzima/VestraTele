<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\components\DialerManager;
use common\modules\lead\models\forms\LeadDialerForm;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\query\LeadDialerQuery;
use common\modules\lead\models\searches\LeadDialerSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * DialerController implements the CRUD actions for LeadDialer model.
 */
class DialerController extends BaseController {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'update-new' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all LeadDialer models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new LeadDialerSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if (Yii::$app->request->isDelete) {
			/** @var LeadDialerQuery $query */
			$query = $dataProvider->query;
			$query->select(LeadDialer::tableName() . '.id');
			LeadDialer::deleteAll(['id' => $query]);
			return $this->refresh();
		}
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionUpdateNew() {
		$manager = new DialerManager();
		$count = $manager->updateNotForDialerStatuses();
		codecept_debug('UPDATE NEW');
		codecept_debug($count);
		if ($count) {
			Flash::add(Flash::TYPE_SUCCESS, Yii::t('lead', 'Success Update: {count} New Dialers with Lead Status not for them.', [
				'count' => $count,
			]));
		}
		return $this->redirect(['index']);
	}

	/**
	 * Displays a single LeadDialer model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new LeadDialer model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate(int $id = null) {
		$model = new LeadDialerForm();
		$model->leadId = $id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionCreateMultiple(array $ids = []) {
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
			Flash::add(Flash::TYPE_WARNING, Yii::t('lead', 'Ids cannot be blank.'));
			return $this->redirect(['lead/index']);
		}
		if (count($ids) === 1) {
			$id = reset($ids);
			return $this->redirect(['create', 'id' => $id]);
		}
		$model = new LeadDialerForm();
		$model->scenario = LeadDialerForm::SCENARIO_MULTIPLE;
		$model->leadId = $ids;
		if ($model->load(Yii::$app->request->post())) {
			$count = $model->saveMultiple();
			if ($count) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('lead', 'Success add Leads: {count} to Dialer.', [
						'count' => $count,
					])
				);
				return $this->redirect(['index']);
			}
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing LeadDialer model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new LeadDialerForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing LeadDialer model.
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
	 * Finds the LeadDialer model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return LeadDialer the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): LeadDialer {
		if (($model = LeadDialer::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}
