<?php

namespace frontend\controllers;

use common\models\issue\IssuePayCalculation;
use common\models\user\Worker;
use frontend\helpers\Url;
use frontend\models\search\IssuePayCalculationSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SettlementController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::PERMISSION_ISSUE],
					],
				],
			],
		];
	}

	public function actionIndex(): string {
		$searchModel = new IssuePayCalculationSearch();
		$ids = Yii::$app->userHierarchy->getAllChildesIds(Yii::$app->user->getId());
		$ids[] = Yii::$app->user->getId();
		$searchModel->issueUsersIds = $ids;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * @param int $id
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): string {
		Url::remember();

		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * @param int $id
	 * @return IssuePayCalculation
	 * @throws NotFoundHttpException
	 */
	private function findModel(int $id): IssuePayCalculation {
		$model = IssuePayCalculation::findOne($id);

		if ($model === null || !IssueController::shouldFind($model->issue)) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
