<?php

namespace common\modules\lead\controllers;

use common\modules\lead\models\searches\DuplicateLeadSearch;
use Yii;

class DuplicateController extends BaseController {

	public function actionIndex(): string {
		$searchModel = new DuplicateLeadSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

}
