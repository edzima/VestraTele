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
use frontend\helpers\Url;
use frontend\models\IssueStageChangeForm;
use frontend\models\search\IssuePayCalculationSearch;
use frontend\models\search\IssueSearch;
use frontend\models\search\IssueUserSearch;
use frontend\models\search\SummonSearch;
use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class IssueController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'permissions' => [Worker::PERMISSION_ISSUE],
						'matchCallback' => function ($rule, Action $action): bool {
							if ($action->id === 'stage') {
								return Yii::$app->user->can(Worker::PERMISSION_ISSUE_STAGE_CHANGE);
							}
							return true;
						},
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
			$searchModel->agentsIds = Yii::$app->userHierarchy->getAllChildesIds(Yii::$app->user->getId());
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionUser(): string {
		$user = Yii::$app->user;

		$searchModel = new IssueUserSearch();

		if ($user->can(Worker::ROLE_CUSTOMER_SERVICE)) {
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
	 * @throws NotFoundHttpException|ForbiddenHttpException
	 */
	public function actionView(int $id): string {
		$model = $this->findModel($id);

		$calculationsDataProvider = null;
		if (Yii::$app->user->can(Worker::ROLE_CUSTOMER_SERVICE)
			|| $model->isForUser(Yii::$app->user->getId())
			|| $model->isForAgents(Yii::$app->userHierarchy->getAllChildesIds(Yii::$app->user->getId()))
		) {
			$search = new IssuePayCalculationSearch();
			$search->issue_id = $id;
			$search->onlyToPayed = false;
			$search->withAgents = false;
			$search->withArchive = true;
			$calculationsDataProvider = $search->search([]);
		}
		$summonDataProvider = (new SummonSearch(['issue_id' => $model->id]))->search([]);
		$summonDataProvider->sort = false;
		$summonDataProvider->pagination = false;
		Url::remember();

		return $this->render('view', [
			'model' => $model,
			'calculationsDataProvider' => $calculationsDataProvider,
			'summonDataProvider' => $summonDataProvider,
		]);
	}

	public function actionStage(int $issueId, int $stageId = null, string $returnUrl = null) {
		$model = new IssueStageChangeForm($this->findModel($issueId));
		if ($stageId !== null) {
			$model->stage_id = $stageId;
		}
		$model->date_at = date($model->dateFormat);
		$model->user_id = Yii::$app->user->getId();
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect($returnUrl ?? ['view', 'id' => $issueId]);
		}
		return $this->render('stage', [
			'model' => $model,
		]);
	}

	/**
	 * Finds the Issue model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Issue the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 * @throws ForbiddenHttpException if the model is Archived and User can not archive permission.
	 */
	private function findModel(int $id): Issue {
		$model = Issue::findOne($id);
		if ($model === null || !Yii::$app->user->canSeeIssue($model)) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		return $model;
	}

}
