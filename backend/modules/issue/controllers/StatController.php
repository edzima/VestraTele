<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\HostIssueStats;
use backend\modules\issue\models\IssueStats;
use common\behaviors\IssueTypeParentIdAction;
use common\helpers\Flash;
use Yii;
use yii\web\Controller;

class StatController extends Controller {

	public function behaviors() {
		return [
			IssueTypeParentIdAction::class,
		];
	}

	public function actionIndex(int $parentTypeId = null): string {
		$model = new IssueStats();
		$model->issueMainTypeId = IssueTypeParentIdAction::validate($parentTypeId);
		$model->load(Yii::$app->request->queryParams);
		$model->validate();

		return $this->render('index', [
			'model' => $model,
		]);
	}

	public function actionChart(int $parentTypeId = null): string {
		$model = new IssueStats();
		$model->issueMainTypeId = IssueTypeParentIdAction::validate($parentTypeId);
		$model->load(Yii::$app->request->queryParams);
		$model->validate();
		return $this->render('chart', [
			'model' => $model,
		]);
	}

	public function actionYear(int $parentTypeId = null, int $year = null) {
		$model = new IssueStats();
		$model->issueMainTypeId = IssueTypeParentIdAction::validate($parentTypeId);
		$model->year = $year;
		$model->validate();
		return $this->render('year', [
			'model' => $model,
		]);
	}

	public function actionDetails(int $parentTypeId = null, int $year = null, int $month = null) {
		$model = new IssueStats();
		$model->issueMainTypeId = IssueTypeParentIdAction::validate($parentTypeId);
		$model->setMonth($month);
		$model->year = $year;
		$model->validate();
		if (Yii::$app->request->isPjax) {
			return $this->renderAjax('details', [
				'model' => $model,
				'widgetId' => 'details',
			]);
		}
		return $this->render('details', [
			'model' => $model,
			'widgetId' => 'details',
		]);
	}

	public function actionHosts(int $year = null, int $month = null) {
		$models = HostIssueStats::createMultiple([], [
			'statConfig' => [
				'month' => !empty($month) ? $month : null,
				'year' => !empty($year) ? $year : null,
			],
		]);
		if (empty($models)) {
			Flash::add(Flash::TYPE_WARNING, Yii::t('backend', 'Hosts not sets.'));
			return $this->redirect(['index']);
		}
		return $this->render('hosts', [
			'models' => $models,
		]);
	}
}
