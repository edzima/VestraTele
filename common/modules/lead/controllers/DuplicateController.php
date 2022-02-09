<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\modules\lead\models\Lead;
use common\modules\lead\models\searches\DuplicateLeadSearch;
use Yii;

class DuplicateController extends BaseController {

	public function actionIndex() {
		$searchModel = new DuplicateLeadSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if (Yii::$app->request->isDelete) {
			$count = Lead::deleteAll(['id' => $searchModel->getAllIds($dataProvider->query)]);
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('lead', 'Delete Leads: {count}.', [
					'count' => $count,
				])
			);
			return $this->refresh();
		}

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

}
