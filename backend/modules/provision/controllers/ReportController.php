<?php

namespace backend\modules\provision\controllers;

use backend\helpers\Url;
use common\helpers\Flash;
use common\models\provision\Provision;
use common\models\provision\ProvisionReportSearch;
use common\models\provision\ProvisionReportSummary;
use common\models\provision\ProvisionSearch;
use common\models\provision\ProvisionUsersSearch;
use common\models\provision\search\ReportSearch;
use common\models\user\Worker;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Html;
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

	public function actionReport() {
		$searchModel = new ReportSearch();
		$searchModel->load(Yii::$app->request->queryParams);
		if ($searchModel->user_id) {
			return $this->redirect([
				'view',
				'id' => $searchModel->user_id,
				'dateFrom' => $searchModel->dateFrom,
				'dateTo' => $searchModel->dateTo,
			]);
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);


		return $this->render('report', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);

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
		$user = Worker::findOne($id);
		if ($user === null) {
			throw new NotFoundHttpException();
		}
		Url::remember();

		$searchModel = new ProvisionReportSearch();
		$searchModel->to_user_id = $id;
		$searchModel->dateTo = $dateTo;
		$searchModel->dateFrom = $dateFrom;
		$provisionsDataProvider = $searchModel->search(Yii::$app->request->queryParams);
		if ($searchModel->hasHiddenProvisions()) {
			$link = Html::a('ukryto prowizje', [
					'/provision/provision/index',
					Html::getInputName(ProvisionSearch::instance(), 'dateFrom') => $dateFrom,
					Html::getInputName(ProvisionSearch::instance(), 'dateTo') => $dateTo,
					Html::getInputName(ProvisionSearch::instance(), 'to_user_id') => $user->id,
					Html::getInputName(ProvisionSearch::instance(), 'hide_on_report') => true,
				]
			);
			Yii::$app->session->addFlash('warning', 'W raporcie ' . $link);
		}
		if ($provisionsDataProvider->getTotalCount() > $searchModel->limit) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('provision',
					'Total items count is greater than limit: {limit}. Change smaller dates range.', [
						'limit' => $searchModel->limit,
					]));
		}

		$notSettledCostsDataProvider = $searchModel->getNotSettledCosts();
		$settledCostsDataProvider = $searchModel->getSettledCosts();

		$summary = null;
		if (
			$notSettledCostsDataProvider->getTotalCount() > 0
			|| $settledCostsDataProvider->getTotalCount() > 0
		) {
			$summary = new ProvisionReportSummary([
				'provisions' => $provisionsDataProvider->getModels(),
				'settledCosts' => $settledCostsDataProvider->getModels(),
				'notSettledCosts' => $notSettledCostsDataProvider->getModels(),
			]);
		}

		return $this->render('view', [
			'searchModel' => $searchModel,
			'provisionsDataProvider' => $provisionsDataProvider,
			'notSettledCostsDataProvider' => $notSettledCostsDataProvider,
			'settledCostsDataProvider' => $settledCostsDataProvider,
			'summary' => $summary,
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
