<?php

namespace frontend\modules\account\controllers;

use common\models\user\UserVisible;
use common\models\user\Worker;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TreeController extends Controller {

	/**
	 * @inheritdoc
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
		$worker = Worker::findOne(Yii::$app->user->id);
		if (!$worker) {
			throw new NotFoundHttpException('Only for worker.');
		}
		$dataProvider = new ActiveDataProvider([
			'query' => $worker->getAllChildesQuery()
				->with(['userProfile'])
				->andFilterWhere(['NOT IN', Worker::tableName() . '.id', UserVisible::hiddenUsers($worker->id)]),
		]);
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

}
