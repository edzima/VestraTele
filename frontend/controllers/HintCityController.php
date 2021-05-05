<?php

namespace frontend\controllers;

use common\models\hint\HintCity;
use common\models\user\User;
use frontend\models\HintCityForm;
use frontend\models\search\HintCitySearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class HintCityController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'permissions' => [User::PERMISSION_HINT],
					],
				],
			],
		];
	}

	public function actionIndex(): string {
		$searchModel = new HintCitySearch();
		$searchModel->user_id = Yii::$app->user->getId();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionUpdate(int $id) {
		$model = new HintCityForm($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	private function findModel(int $id): HintCity {
		$model = HintCity::find()
			->andWhere(['id' => $id])
			->andWhere(['user_id' => Yii::$app->user->getId()])
			->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}

}
