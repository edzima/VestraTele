<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-13
 * Time: 14:31
 */

namespace frontend\controllers;

use common\models\issue\Issue;
use common\models\user\Worker;
use frontend\models\ClientIssueSearch;
use frontend\models\IssueSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class IssueController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::ROLE_ISSUE],
					],
				],
			],
		];
	}

	public function actionSearch(): string {
		$user = Yii::$app->user;

		if (!$user->can(Worker::ROLE_CUSTOMER_SERVICE)) {
			throw new NotFoundHttpException();
		}

		$searchModel = new ClientIssueSearch();
		if ($user->can(Worker::ROLE_ARCHIVE)) {
			$searchModel->withArchive = true;
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('clientSearch', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all Issue models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$user = Yii::$app->user;
		$searchModel = new IssueSearch();
		if ($user->can(Worker::ROLE_ARCHIVE)) {
			$searchModel->withArchive = true;
		}
		$searchModel->user_id = $user->getId();

		if ($user->can(Worker::ROLE_LAWYER)) {
			$searchModel->isLawyer = true;
		}
		if ($user->can(Worker::ROLE_AGENT)) {
			$worker = Worker::findOne($user->id);
			if ($worker) {
				$searchModel->agents = $worker->getAllChildesIds();
				$searchModel->agents[] = $worker->id;
				$searchModel->isAgent = true;
			}
		}
		if ($user->can(Worker::ROLE_TELEMARKETER)) {
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
	public function actionView(int $id) {
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
			->with('issueNotes.user.userProfile')
			->one();

		if ($model !== null && static::shouldFind($model)) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

	private static function shouldFind(Issue $model): bool {
		$user = Yii::$app->user;
		if ($user->can(Worker::ROLE_ADMINISTRATOR)) {
			return true;
		}
		if ($model->isArchived() && !$user->can(Worker::ROLE_ARCHIVE)) {
			Yii::warning('User: ' . $user->id . ' try view archived issue: ' . $model->id, 'issue');
			return false;
		}
		if (
			$user->can(Worker::ROLE_CUSTOMER_SERVICE)
			|| ($user->can(Worker::ROLE_TELEMARKETER) && $model->tele_id === $user->id)
			|| ($user->can(Worker::ROLE_LAWYER) && $model->lawyer_id === $user->id)) {
			return true;
		}

		if ($user->can(Worker::ROLE_AGENT)) {
			$worker = Worker::findOne($user->id);
			if ($worker) {
				$agents = $worker->getAllChildesIds();
				$agents[] = $worker->id;
				if (in_array($model->agent_id, $agents)) {
					return true;
				}
			}
		}
		return false;
	}

}
