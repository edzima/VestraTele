<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueUserForm;
use backend\modules\issue\models\search\UserSearch;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use Yii;
use yii\base\InvalidArgumentException;
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

	public function actionCreate(int $issueId) {
		$model = new IssueUserForm();
		$model->issue_id = $issueId;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['issue', 'id' => $issueId]);
		}
	}

	/**
	 * Creates a new IssueUser model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string
	 * @throws BadRequestHttpException
	 */
	public function actionLink(int $userId) {
		try {
			$model = new IssueUserForm($userId);
		} catch (InvalidArgumentException $exception) {
			throw new BadRequestHttpException($exception->getMessage());
		}
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['issue', 'id' => $model->issue_id]);
		}
		return $this->render('link', [
			'model' => $model,
		]);
	}

	/**
	 * Displays a single IssueStage model.
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
	 * Updates an existing IssueStage model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $issueId
	 * @param string $type
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function actionUpdate(int $issueId, string $type) {
		$issue = $this->findIssue($issueId);
		$model = new IssueUserForm();
		$model->type = $type;
		$model->setIssue($issue);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}
		return $this->render('update', [
			'model' => $model,
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
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function actionDelete(int $issueId, int $userId, string $type) {
		$this->findModel($issueId, $userId, $type)->delete();

		return $this->redirect(['index']);
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
