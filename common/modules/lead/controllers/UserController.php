<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\helpers\Url;
use common\modules\lead\models\forms\LeadsUserForm;
use Yii;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\searches\LeadUsersSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for LeadUser model.
 */
class UserController extends BaseController {

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
	 * Lists all LeadUser models.
	 *
	 * @return string|Response
	 */
	public function actionIndex() {
		$searchModel = new LeadUsersSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if (strcasecmp(Yii::$app->request->method, 'delete') === 0) {
			LeadUsersSearch::deleteAll($dataProvider->query->where);
			return $this->refresh();
		}

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionAssignSingle(int $id) {
		$lead = $this->findLead($id);
		$model = new LeadsUserForm();
		$model->scenario = LeadsUserForm::SCENARIO_SINGLE;
		$model->withOwner = !$this->module->onlyUser;
		$model->leadsIds = [$lead->getId()];
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('lead', 'Success assign {user} as {type} to Lead: {lead}.', [
					'user' => LeadsUserForm::getUsersNames()[$model->userId],
					'type' => $model->getTypesNames()[$model->type],
					'lead' => $lead->getName(),
				])
			);
			$model->sendEmail();
			return $this->redirect(['lead/view', 'id' => $id]);
		}
		return $this->render('assign-single', [
			'model' => $model,
			'lead' => $lead,
		]);
	}

	public function actionAssign(array $ids = []) {
		if (empty($ids)) {
			$postIds = Yii::$app->request->post('leadsIds');
			if (is_string($postIds)) {
				$postIds = explode(',', $postIds);
			}
			if ($postIds) {
				$ids = $postIds;
			}
		}
		$model = new LeadsUserForm();
		$ids = array_unique($ids);
		$model->leadsIds = array_combine($ids, $ids);
		if ($model->load(Yii::$app->request->post())) {
			$count = $model->save();
			if ($count) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('lead', 'Success assign {user} as {type} to {count} leads.', [
						'user' => LeadsUserForm::getUsersNames()[$model->userId],
						'type' => $model->getTypesNames()[$model->type],
						'count' => $count,
					])
				);
				$model->sendEmail();
				return $this->redirect(Url::previous());
			}
		}
		return $this->render('assign', [
			'model' => $model,
		]);
	}

	/**
	 * Displays a single LeadUser model.
	 *
	 * @param integer $lead_id
	 * @param integer $user_id
	 * @param string $type
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $lead_id, int $user_id, string $type) {
		return $this->render('view', [
			'model' => $this->findModel($lead_id, $user_id, $type),
		]);
	}

	/**
	 * Creates a new LeadUser model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new LeadUser();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'lead_id' => $model->lead_id, 'user_id' => $model->user_id, 'type' => $model->type]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing LeadUser model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $lead_id
	 * @param integer $user_id
	 * @param string $type
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $lead_id, int $user_id, string $type) {
		$model = $this->findModel($lead_id, $user_id, $type);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'lead_id' => $model->lead_id, 'user_id' => $model->user_id, 'type' => $model->type]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing LeadUser model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $lead_id
	 * @param integer $user_id
	 * @param string $type
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $lead_id, int $user_id, string $type) {
		$this->findModel($lead_id, $user_id, $type)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the LeadUser model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $lead_id
	 * @param integer $user_id
	 * @param string $type
	 * @return LeadUser the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $lead_id, int $user_id, string $type): LeadUser {
		if (($model = LeadUser::findOne(['lead_id' => $lead_id, 'user_id' => $user_id, 'type' => $type])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('lead', 'The requested page does not exist.'));
	}
}
