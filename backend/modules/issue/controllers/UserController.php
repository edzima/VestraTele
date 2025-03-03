<?php

namespace backend\modules\issue\controllers;

use backend\helpers\Url;
use backend\modules\issue\models\IssueUserForm;
use backend\modules\issue\models\search\UserSearch;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\User;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
	public function actionIndex(): string {
		$searchModel = new UserSearch();
		if (Yii::$app->user->can(User::PERMISSION_ARCHIVE)) {
			$searchModel->withArchive = true;
		}

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
		if (Yii::$app->user->can(User::PERMISSION_ARCHIVE)) {
			$searchModel->withArchive = true;
		}
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
	 * @return string|Response
	 */
	public function actionLink(int $userId = null, int $issueId = null) {
		$model = new IssueUserForm();
		$model->issue_id = $issueId;
		$model->user_id = $userId;
		$model->withArchive = Yii::$app->user->can(User::PERMISSION_ARCHIVE);

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
		$model->withArchive = Yii::$app->user->can(User::PERMISSION_ARCHIVE);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if ($model->type !== $type) {
				$issue->unlinkUser($type);
			}
			return $this->redirect(['/issue/issue/view', 'id' => $issueId]);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionCustomerIssues(string $q) {
		Yii::$app->response->format = Response::FORMAT_JSON;
		$out = ['results' => ['id' => '', 'text' => '']];
		if (!empty($q) && strlen($q) >= 3) {
			$models = IssueUser::find()
				->withType(IssueUser::TYPE_CUSTOMER)
				->withUserFullName($q)
				->joinWith('issue')
				->orderBy([
					Issue::tableName() . '.created_at' => SORT_DESC,
				])
				->all();
			if (!empty($models)) {
				$results = [];
				foreach ($models as $model) {
					$results[] = [
						'id' => $model->issue_id,
						'text' => $model->issue->getIssueName() . ' - ' . $model->user->getFullName(),
					];
				}
				$out['results'] = $results;
			}
		}
		return $out;
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
			//	IssueUser::TYPE_LAWYER,
			IssueUser::TYPE_AGENT,
		];
		if (in_array($type, $required, true)) {
			throw new BadRequestHttpException('Invalid Type');
		}
		$issue = $this->findIssue($issueId);
		$issue->unlinkUser($type);
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
