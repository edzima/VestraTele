<?php

namespace frontend\controllers;

use common\models\issue\search\SummonDocLinkSearch;
use yii\web\Controller;

class SummonDocController extends Controller {

	public function actionToDo(): string {
		$searchModel = new SummonDocLinkSearch();
		$dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

		return $this->render('to-do', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
}
