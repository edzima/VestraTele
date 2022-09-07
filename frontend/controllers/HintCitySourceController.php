<?php

namespace frontend\controllers;

use common\models\hint\HintCity;
use common\models\hint\HintCitySource;
use common\models\user\User;
use frontend\models\HintCitySourceForm;
use frontend\models\search\HintCitySourceSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class HintCitySourceController extends Controller {

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
		$searchModel = new HintCitySourceSearch();
		$searchModel->user_id = Yii::$app->user->getId();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreate(int $id) {
		$model = new HintCitySourceForm();
		$model->setHintCity($this->findHint($id));
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['/hint-city/view', 'id' => $id]);
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionUpdate(int $hint_id, int $source_id) {
		$model = new HintCitySourceForm();
		$model->setModel($this->findSource($hint_id, $source_id));
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['/hint-city/view', 'id' => $hint_id]);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	private function findHint(int $id): HintCity {
		$model = HintCity::find()
			->andWhere(['id' => $id])
			->andWhere(['user_id' => Yii::$app->user->getId()])
			->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}

	private function findSource(int $hint_id, int $source_id): HintCitySource {
		$model = HintCitySource::find()
			->andWhere(['hint_id' => $hint_id, 'source_id' => $source_id])
			->one();
		if ($model === null || $model->hint->user_id !== Yii::$app->user->getId()) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
