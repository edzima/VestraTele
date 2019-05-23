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

	/**
	 * Lists all Issue models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new IssueSearch();
		$user = Yii::$app->user;
		$searchModel->user_id = $user->getId();
		if ($user->can(User::ROLE_LAYER)) {
			$searchModel->isLawyer = true;
		}
		if ($user->can(User::ROLE_AGENT)) {
			$searchModel->agents = $user->getIdentity()->getAllChildsIds();
			$searchModel->agents[] = $user->id;
			$searchModel->isAgent = true;
		}
		if ($user->can(User::ROLE_TELEMARKETER)) {
			$searchModel->isTele = true;
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
			->andWhere(['id' => $id])
			->one();

		if ($model !== null && static::shouldFind($model)) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

	private static function shouldFind(Issue $model): bool {
		$user = Yii::$app->user;
		if ($user->can(User::ROLE_TELEMARKETER) && $model->tele_id === $user->id) {
			return true;
		}
		if ($user->can(User::ROLE_LAYER) && $model->lawyer_id === $user->id) {
			return true;
		}
		if ($user->can(User::ROLE_AGENT)) {
			$agents = $user->getIdentity()->getAllChildsIds();
			$agents[] = $user->id;
			if (in_array($model->agent_id, $agents)) {
				return true;
			}
		}
		return false;
	}

}