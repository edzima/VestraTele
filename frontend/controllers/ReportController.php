<?php

namespace frontend\controllers;

use common\models\provision\ProvisionReportSearch;
use common\models\user\Worker;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

class ReportController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionIndex(int $user_id = null): string {
		$currentUserId = Yii::$app->user->getId();
		$searchModel = new ProvisionReportSearch();
		$searchModel->to_user_id = $currentUserId;
		if ($user_id !== null && $user_id !== $currentUserId) {
			if (!Yii::$app->user->can(Worker::PERMISSION_PROVISION_CHILDREN_VISIBLE)) {
				Yii::warning("User: $currentUserId without permission try visible child: $user_id provision.");
				throw new MethodNotAllowedHttpException();
			}
			$ids = Yii::$app->userHierarchy->getAllChildesIds($currentUserId);
			//@todo check strict mode after merge with relation branch.
			if (!in_array($user_id, $ids)) {
				Yii::warning("User: $currentUserId with permission try visible not self child: $user_id provision.");
				throw new NotFoundHttpException();
			}
			$searchModel->to_user_id = $user_id;
		}

		$searchModel->load(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
		]);
	}

}
