<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\searches\LeadChartSearch;
use Yii;
use yii\web\Controller;

class ChartController extends Controller {

	public function actionIndex(): string {
		$searchModel = new LeadChartSearch();
		$searchModel->load(Yii::$app->request->queryParams);
		$searchModel->validate();
		return $this->render('index', [
			'searchModel' => $searchModel,
		]);
	}

	public function actionApex(): string {
		return $this->render('apex');
	}
}
