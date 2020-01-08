<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-16
 * Time: 09:24
 */

namespace frontend\modules\account\controllers;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

class TreeController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors():array {
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
		/** @var User $user $user */
		$user = Yii::$app->user->identity;
		$dataProvider = new ActiveDataProvider([
			'query' => $user->getAllChildesQuery()->with(['userProfile']),
		]);
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}

}
