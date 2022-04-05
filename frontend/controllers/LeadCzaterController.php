<?php

namespace frontend\controllers;

use common\modules\czater\entities\Call;
use Yii;
use yii\rest\Controller;

class LeadCzaterController extends Controller {

	public function actionConvBegin(): void {
		Yii::warning([
			'get' => Yii::$app->request->get(),
			'post' => Yii::$app->request->post(),
			'bodyParams' => Yii::$app->request->getBodyParams(),
			'queryParams' => Yii::$app->request->getQueryParams(),
		], 'lead.czater.convBegin');
	}

	public function actionConvEnd(): void {
		Yii::warning([
			'get' => Yii::$app->request->get(),
			'post' => Yii::$app->request->post(),
			'queryParams' => Yii::$app->request->getQueryParams(),
		], 'lead.czater.convEnd');
	}

	public function actionCallEnd(): void {

		$call = new Call();
		Yii::warning([
			'get' => Yii::$app->request->get(),
			'post' => Yii::$app->request->post(),
			'queryParams' => Yii::$app->request->getQueryParams(),

		], 'lead.czater.callEnd');
	}
}
