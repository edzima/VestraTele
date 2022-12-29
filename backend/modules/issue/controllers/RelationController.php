<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\search\RelationSearch;
use common\models\issue\Issue;
use common\models\issue\IssueRelation;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * RelationController implements the CRUD actions for IssueRelation model.
 */
class RelationController extends Controller {

	/**
	 * {@inheritdoc}
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
	 * Lists all IssueRelation models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new RelationSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssueRelation model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new IssueRelation model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate(int $id) {
		$issue = Issue::findOne($id);
		if ($issue === null) {
			throw new NotFoundHttpException();
		}
		$model = new IssueRelation();
		$model->issue_id_1 = $id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['issue/view', 'id' => (int) $model->issue_id_2]);
		}

		return $this->render('create', [
			'model' => $model,
			'issue' => $issue,
		]);
	}

	/**
	 * Updates an existing IssueRelation model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueRelation model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id, string $returnUrl = null) {
		$this->findModel($id)->delete();

		if ($returnUrl) {
			return $this->redirect($returnUrl);
		}
		return $this->redirect(['index']);
	}

	/**
	 * Finds the IssueRelation model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueRelation the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = IssueRelation::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('issue', 'The requested page does not exist.'));
	}
}
