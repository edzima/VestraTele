<?php

namespace common\modules\lead\controllers;

use common\models\user\User;
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
		$campaignsCost = [];
		if ($searchModel->validate() && Yii::$app->user->can(User::PERMISSION_LEAD_COST)) {
			$campaignsIds = array_keys($searchModel->getLeadCampaignsCount());
			$campaignsIds = array_filter($campaignsIds, function ($campaignId) {
				return !empty($campaignId);
			});
			if (!empty($campaignsIds)) {
				$campaignsCost = $this->module->getCost()
					->recalculateFromDate(
						$searchModel->from_at,
						$searchModel->to_at,
						$campaignsIds
					);
			}
		}

		return $this->render('index', [
			'searchModel' => $searchModel,
			'campaignsCost' => $campaignsCost,
		]);
	}

}
