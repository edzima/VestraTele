<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\search\WorkersWithoutIssuesSearch;
use backend\modules\user\models\search\WorkerUserSearch;
use backend\modules\user\models\WorkerUserForm;
use common\helpers\Flash;
use common\models\forms\HierarchyForm;
use common\models\user\Worker;
use Yii;

class WorkerController extends UserController {

	public $searchModel = WorkerUserSearch::class;
	public $formModel = WorkerUserForm::class;
	public $model = Worker::class;

	/**
	 * Lists all User models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$searchModel = new $this->searchModel();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if (!Yii::$app->user->can(Worker::PERMISSION_WORKERS)) {
			if (empty($searchModel->lastname) || !$searchModel->validate(['lastname'])) {
				$dataProvider->query->andWhere('0=1');
			}
		}

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionHierarchy(int $id) {
		/** @var Worker $user */
		$user = $this->findModel($id);
		$model = new HierarchyForm(Yii::$app->userHierarchy);
		$map = Worker::getSelectList(
			Yii::$app->authManager->getUserIdsByRole(Worker::ROLE_AGENT)
		);
		if (isset($map[$id])) {
			unset($map[$id]);
		}
		$model->parentsMap = $map;
		$model->id = $user->id;
		$model->parent_id = $user->getParentId();
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}
		return $this->render('hierarchy', [
			'user' => $user,
			'model' => $model,
		]);
	}

	public function actionWithoutIssues(): string {
		$searchModel = new WorkersWithoutIssuesSearch();
		$searchModel->createdAtFrom = date(DATE_ATOM, strtotime('-3 months'));
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('without-issues', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionChangeStatus(int $status, string $returnUrl = null) {
		$ids = Yii::$app->request->post('selection');
		if (empty($ids)) {
			Flash::add(
				Flash::TYPE_ERROR,
				Yii::t('backend', 'Ids must be set.')
			);
			return $this->redirect(['index']);
		}

		$count = Worker::updateAll(['status' => $status], ['id' => $ids]);
		if ($count) {
			Flash::add(
				Flash::TYPE_SUCCESS,
				Yii::t('backend', 'Status changed successfully.')
			);
		}
		return $this->redirect($returnUrl ? $returnUrl : ['index']);
	}

}
