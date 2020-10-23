<?php

namespace backend\modules\issue\controllers;

use backend\helpers\Url;
use backend\modules\issue\models\IssueUserForm;
use backend\modules\issue\models\search\UserSearch;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for IssueUser model.
 */
class UserController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all IssueUser models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new UserSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all IssueUser models for Issue.
	 *
	 * @return mixed
	 */
	public function actionIssue(int $id) {
		$model = $this->findIssue($id);
		$searchModel = new UserSearch();
		$searchModel->issue_id = $id;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('issue', [
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Link a User to Issue.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionLink(int $userId) {
		$model = new IssueUserForm();
		$model->user_id = $userId;
		if ($model->getUser() === null) {
			throw new NotFoundHttpException();
		}
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(Url::issueView($model->issue_id));
		}
		return $this->render('link', [
			'model' => $model,
		]);
	}

	public function actionUpdateType(int $issueId, int $userId, string $type) {
		$model = new IssueUserForm();
		$issue = $this->findIssue($issueId);
		$model->setIssue($issue);
		$model->user_id = $userId;
		$model->type = $type;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if ($model->type !== $type) {
				$issue->unlinkUser($type, true);
			}
			return $this->redirect(['/issue/issue/view', 'id' => $issueId]);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Displays a single IssueUser model.
	 *
	 * @param int $issueId
	 * @param int $userId
	 * @param string $type
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $issueId, int $userId, string $type) {
		return $this->render('view', [
			'model' => $this->findModel($issueId, $userId, $type),
		]);
	}

	/**
	 * Deletes an existing IssueStage model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $issueId
	 * @param int $userId
	 * @param string $type
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $issueId, int $userId, string $type) {
		$required = [
			IssueUser::TYPE_CUSTOMER,
			IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_AGENT,
		];
		if (in_array($type, $required, true)) {
			throw new BadRequestHttpException('Invalid Type');
		}

		$this->findModel($issueId, $userId, $type)->delete();
		return $this->redirect(Url::issueView($issueId));
	}

	/**
	 * Finds the IssueUser model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $issueId
	 * @param int $userId
	 * @param string $type
	 * @return IssueUser the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $issueId, int $userId, string $type): IssueUser {
		if (($model = IssueUser::find()->andWhere([
				'user_id' => $userId,
				'issue_id' => $issueId,
				'type' => $type,
			])->one()) !== null) {
			{
				return $model;
			}
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

	/**
	 * @param int $id
	 * @return Issue
	 * @throws NotFoundHttpException
	 */
	protected function findIssue(int $id): Issue {
		$model = Issue::findOne($id);
		if ($model instanceof Issue) {
			return $model;
		}
		throw new NotFoundHttpException('Issue not found.');
	}

}
