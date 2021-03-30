<?php

namespace frontend\controllers;

use common\models\provision\ProvisionReportSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ReportController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	public function actionIndex(): string {
		$searchModel = new ProvisionReportSearch();
		$searchModel->to_user_id = Yii::$app->user->getId();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

}
