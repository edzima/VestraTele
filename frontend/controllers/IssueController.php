<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-13
 * Time: 14:31
 */

namespace frontend\controllers;

use common\models\issue\Issue;
use common\models\user\User;
use common\models\user\Worker;
use frontend\models\search\IssueSearch;
use frontend\models\search\IssueUserSearch;
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
						'roles' => [Worker::PERMISSION_ISSUE],
					],
				],
			],
		];
	}

	/**
	 * Lists all Issue models available for current User.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$user = Yii::$app->user;
		$searchModel = new IssueSearch();
		if ($user->can(Worker::PERMISSION_ARCHIVE)) {
			$searchModel->withArchive = true;
		}
		$searchModel->user_id = (int) $user->getId();

		if ($user->can(Worker::ROLE_AGENT)) {
			$worker = Worker::findOne($user->id);
			if ($worker) {
				$searchModel->agentsIds = $worker->getAllChildesIds();
			}
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionTest(): string {
		$sender = getenv('EMAIL_ROBOT');
		Yii::info('senderrrr');
		Yii::info($sender);

		$send = Yii::$app->mailer->compose()
			->setFrom(getenv('EMAIL_ROBOT'))
			->setTo('atipezda@gmail.com')
			->setSubject('Message subject')
			->setTextBody('Plain text content')
			->setHtmlBody('<b>HTML content</b>')
			->send();


		return $send;
	}

	public function actionUser(): string {
		$user = Yii::$app->user;

		$searchModel = new IssueUserSearch();

		if ($user->can(User::ROLE_CUSTOMER_SERVICE)) {
			$searchModel->withArchive = true;
		} else {
			$searchModel->user_id = $user->id;
		}
		if ($user->can(Worker::PERMISSION_ARCHIVE)) {
			$searchModel->withArchive = true;
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('user', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Issue model.
	 *
	 * @param integer $id
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): string {
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
	public static function findModel(int $id): Issue {
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
		if ($user->can(Worker::ROLE_ADMINISTRATOR)) {
			return true;
		}
		if ($model->isArchived() && !$user->can(Worker::PERMISSION_ARCHIVE)) {
			Yii::warning('User: ' . $user->id . ' try view archived issue: ' . $model->id, 'issue');
			return false;
		}
		if ($user->can(Worker::ROLE_CUSTOMER_SERVICE) || $model->isForUser($user->id)) {
			return true;
		}

		if ($user->can(Worker::ROLE_AGENT)) {
			$agent = Worker::findOne($user->id);
			if ($agent) {
				$childesIds = $agent->getAllChildesIds();
				if (!empty($childesIds)) {
					return $model->isForAgents($childesIds);
				}
			}
		}
		return false;
	}

}
