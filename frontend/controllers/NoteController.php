<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-13
 * Time: 14:31
 */

namespace frontend\controllers;

use backend\modules\issue\models\IssueNoteForm;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class NoteController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],
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
	 * Creates a new IssueNote model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate(int $issueId) {
		$note = new IssueNote();
		$note->issue_id = $this->findIssueModel($issueId)->id;
		$note->user_id = Yii::$app->user->id;

		$model = new IssueNoteForm($note);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$this->redirectIssue($issueId);
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueNote model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate(int $id) {
		$model = new IssueNoteForm($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$this->redirectIssue($model->getNote()->issue_id);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueNote model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$model = $this->findModel($id);
		$issueId = $model->issue_id;
		$model->delete();
		$this->redirectIssue($issueId);
	}

	private function redirectIssue(int $issueId) {
		return $this->redirect(['issue/view', 'id' => $issueId, '#' => 'notes-list']);
	}

	/**
	 * Finds the IssueNote model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueNote the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = IssueNote::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

	protected function findIssueModel(int $id): Issue {
		return IssueController::findModel($id);
	}

}