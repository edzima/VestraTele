<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\searches\LeadChartSearch;
use common\modules\lead\models\searches\LeadSearch;
use Yii;

class ChartController extends BaseController {

	public function actionIndex(): string {
		$searchModel = new LeadChartSearch();
		if ($this->module->onlyUser) {
			$searchModel->setScenario(LeadSearch::SCENARIO_USER);
			$searchModel->user_id = Yii::$app->user->getId();
		}
		$searchModel->load(Yii::$app->request->queryParams);
		$searchModel->validate();
		return $this->render('index', [
			'searchModel' => $searchModel,
		]);
	}

}