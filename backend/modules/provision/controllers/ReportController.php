<?php

namespace backend\modules\provision\controllers;

use backend\helpers\Url;
use common\helpers\Flash;
use common\models\provision\Provision;
use common\models\provision\ProvisionReportSearch;
use common\models\provision\ProvisionSearch;
use common\models\provision\ToUserGroupProvisionSearch;
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
					'hide' => ['POST'],
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
		$searchModel = new ToUserGroupProvisionSearch();
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
		Url::remember();

		$searchModel = new ProvisionReportSearch();
		$searchModel->to_user_id = $id;
		$searchModel->dateTo = $dateTo;
		$searchModel->dateFrom = $dateFrom;
		if ($searchModel->hasHiddenProvisions()) {
			$link = Html::a(Yii::t('provision', 'hidden provisions'), [
					'/provision/provision/index',
					Html::getInputName(ProvisionSearch::instance(), 'dateFrom') => $dateFrom,
					Html::getInputName(ProvisionSearch::instance(), 'dateTo') => $dateTo,
					Html::getInputName(ProvisionSearch::instance(), 'to_user_id') => $id,
					Html::getInputName(ProvisionSearch::instance(), 'hide_on_report') => true,
				]
			);
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('provision', 'In report' . ' ' . $link)
			);
		}

		return $this->render('view', [
			'searchModel' => $searchModel,
		]);
	}

	public function actionHide(int $id): void {
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
