<?php

namespace backend\modules\provision\controllers;

use backend\helpers\Url;
use common\models\provision\Provision;
use common\models\provision\ProvisionReportSearch;
use common\models\provision\ProvisionUsersSearch;
use common\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ReportController extends Controller {

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
	 * Lists all reports models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new ProvisionUsersSearch();
		$searchModel->load(Yii::$app->request->queryParams);
		if ($searchModel->to_user_id) {
			return $this->redirect([
				'view',
				'id' => $searchModel->to_user_id,
				'dateFrom' => $searchModel->dateFrom,
				'dateTo' => $searchModel->dateTo,
			]);
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionView(int $id, string $dateTo, string $dateFrom): string {
		$user = User::findOne($id);
		if ($user === null) {
			throw new NotFoundHttpException();
		}
		Url::remember();

		$searchModel = new ProvisionReportSearch();
		$searchModel->setToUser($user);
		$searchModel->dateTo = $dateTo;
		$searchModel->dateFrom = $dateFrom;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('view', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionDelete(int $id): void {
		$model = $this->findModel($id);
		$model->hide_on_report = true;
		$model->save(false);
		$this->goBack();
	}

	private function findModel(int $id): Provision {
		$model = Provision::findOne($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}

}
