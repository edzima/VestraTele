<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\search\WorkerUserSearch;
use backend\modules\user\models\WorkerUserForm;
use common\models\relation\HierarchyForm;
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

}
