<?php

namespace backend\controllers;

use common\models\AddressSearch;
use Yii;
use yii\web\Controller;

class AddressController extends Controller {

	public function actionIndex(): string {
		$searchModel = new AddressSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
}
