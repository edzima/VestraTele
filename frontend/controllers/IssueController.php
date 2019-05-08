<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-13
 * Time: 14:31
 */

namespace frontend\controllers;

use common\models\issue\Issue;
use common\models\User;
use frontend\models\IssueSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class IssueController extends Controller {

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	/**
	 * Lists all Issue models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new IssueSearch();
		$user = Yii::$app->user;

		if ($user->can(User::ROLE_LAYER)) {
			$searchModel->lawyer_id = $user->id;
		}
		if ($user->can(User::ROLE_AGENT)) {
			$searchModel->agents = $user->getIdentity()->getAllChildsIds();
			$searchModel->agents[] = $user->id;
			$searchModel->agent_id = $user->id;
		}
		if ($user->can(User::ROLE_TELEMARKETER)) {
			$searchModel->tele_id = $user->id;
		}

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Issue model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', [
			'model' => static::findModel($id),
		]);
	}

	/**
	 * Finds the Issue model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Issue the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public static function findModel($id): Issue {
		$model = Issue::find()
			->where(['id' => $id]);
		$user = Yii::$app->user;

		if ($user->can(User::ROLE_AGENT)) {
			$agents = $user->getIdentity()->getAllChildsIds();
			$agents[] = $user->id;
			$model->onlyForAgents($agents);
		}
		if ($user->can(User::ROLE_LAYER)) {
			$model->onlyForLawyer($user->id);
		}
		if ($user->can(User::ROLE_TELEMARKETER)) {
			$model->onlyForTele($user->id);
		}

		$model = $model->one();
		if ($model !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

}