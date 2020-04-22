<?php

namespace backend\modules\issue\controllers;

use backend\widgets\CsvForm;
use common\models\issue\Issue;
use common\models\issue\IssuePayQuery;
use common\models\User;
use Yii;
use common\models\issue\IssuePay;
use backend\modules\issue\models\searches\IssuePaySearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2tech\csvgrid\CsvGrid;

/**
 * PayController implements the CRUD actions for IssuePay model.
 */
class PayController extends Controller {

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
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [User::ROLE_BOOKKEEPER],
					],
				],
			],

		];
	}

	/**
	 * Lists all IssuePay models.
	 *
	 * @param int $status
	 * @return mixed
	 */
	public function actionIndex(int $status = IssuePaySearch::PAY_STATUS_ACTIVE) {

		$searchModel = new IssuePaySearch();
		$searchModel->setPayStatus($status);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		if (isset($_POST[CsvForm::BUTTON_NAME])) {
			/* @var IssuePayQuery $query */
			$query = $dataProvider->query;
			$query->groupBy('issue_id');

			$exporter = new CsvGrid([
				'query' => $query,
				'columns' => [
					/*
					[
						'attribute' => 'issue.longId',
						'label' => 'Sprawa',
					],
					[
						'attribute' => 'issue.clientFullName',
						'label' => 'Klient',
					],
					*/
					[
						'attribute' => 'issue.client_phone_1',
						'label' => 'Telefon',
					],
					/*
					[
						'label' => 'Tele',
						'content' => function (IssuePay $model): ?string {
							if ($model->issue->tele_id) {
								return User::getUserName($model->issue->tele_id);
							}
							return null;
						},
					],
					[
						'label' => 'Agent',
						'content' => function (IssuePay $model): string {
							return User::getUserName($model->issue->agent_id);
						},
					],
					*/
				],
			]);
			return $exporter->export()->send('export.csv');
		}
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionPay(int $id) {
		$model = $this->findModel($id);
		$isPayed = $model->isPayed();
		if (!$model->isPayed()) {
			$model->pay_at = date(DATE_ATOM);
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$this->redirect('index');
		}
		return $this->render('pay', [
			'model' => $model,
			'isPayed' => $isPayed,
		]);
	}

	public function actionStatus(int $id) {
		$model = $this->findModel($id);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$this->redirect('index');
		}
		return $this->render('status', [
			'model' => $model,
		]);
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
