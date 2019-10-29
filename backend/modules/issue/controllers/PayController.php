<?php

namespace backend\modules\issue\controllers;

use common\models\issue\Issue;
use common\models\User;
use Yii;
use common\models\issue\IssuePay;
use backend\modules\issue\models\searches\IssuePaySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PayController implements the CRUD actions for IssuePay model.
 */
class PayController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'pay' => ['POST'],
				],
			],

		];
	}

	public function beforeAction($action) {
		if (!Yii::$app->user->can(User::ROLE_BOOKKEEPER)) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		return parent::beforeAction($action);
	}

	/**
	 * Lists all IssuePay models.
	 *
	 * @param int $status
	 * @return mixed
	 */
	public function actionIndex(int $status = IssuePaySearch::STATUS_ACTIVE) {
		$searchModel = new IssuePaySearch();
		$searchModel->setStatus($status);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssuePay model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	public function actionPay(int $id) {
		$model = $this->findModel($id);
		$model->markAsPay();
		$this->redirect('index');
	}

	/**
	 * Finds the IssuePay model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssuePay the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id): IssuePay {
		if (($model = IssuePay::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

	protected function findIssueModel(int $id): Issue {
		if (($model = Issue::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
