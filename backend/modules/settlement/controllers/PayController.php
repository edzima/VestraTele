<?php

namespace backend\modules\settlement\controllers;

use backend\helpers\Url;
use backend\modules\settlement\models\search\IssuePaySearch;
use backend\widgets\CsvForm;
use common\components\provision\exception\MissingProvisionUserException;
use common\helpers\Flash;
use common\models\issue\IssuePay;
use common\models\issue\query\IssuePayQuery;
use common\models\settlement\PayPayedForm;
use common\models\settlement\search\DelayedIssuePaySearch;
use common\models\user\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
					'pay-provisions' => ['POST'],
				],
			],
		];
	}

	public function actionPayProvisions(): string {
		if (isset($_POST['expandRowKey'])) {
			$model = $this->findModel($_POST['expandRowKey']);
			$userId = Yii::$app->user->getId();
			if (!Yii::$app->user->can(User::PERMISSION_PROVISION)
				&& !$model->calculation->issue->isForUser($userId)) {
				throw new NotFoundHttpException();
			}
			$query = $model->getProvisions()
				->joinWith('toUser.userProfile')
				->joinWith('fromUser.userProfile');
			if (!Yii::$app->user->can(User::PERMISSION_PROVISION)) {
				$query->user($userId);
			}
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
			]);
			return $this->renderPartial('_pay_provisions', [
				'dataProvider' => $dataProvider,
			]);
		}
		return '<div class="alert alert-danger">No data found</div>';
	}

	public function actionDelayed(): string {
		Url::remember();
		$searchModel = new DelayedIssuePaySearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all IssuePay models.
	 *
	 * @param int $status
	 * @return mixed
	 */
	public function actionIndex(string $status = IssuePaySearch::PAY_STATUS_NOT_PAYED) {
		$searchModel = new IssuePaySearch();
		if (!Yii::$app->user->can(User::ROLE_ADMINISTRATOR)) {
			$searchModel->calculationOwnerId = Yii::$app->user->getId();
		}
		Url::remember();
		$searchModel->payStatus = $status;
		$searchModel->delay = null;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if (isset($_POST[CsvForm::BUTTON_NAME])) {
			/* @var IssuePayQuery $query */
			$query = $dataProvider->query;
			$query->groupBy('P.calculation_id');

			$exporter = new CsvGrid([
				'query' => $query,
				'columns' => [
					[
						'attribute' => 'calculation.issue.longId',
						'label' => Yii::t('issue', 'Issue'),
					],
					[
						'attribute' => 'calculation.typeName',
						'label' => Yii::t('settlement', 'Settlement'),
					],
					[
						'attribute' => 'calculation.value',
						'label' => Yii::t('settlement', 'Value'),
					],
					[
						'attribute' => 'calculation.valueToPay',
						'label' => Yii::t('settlement', 'Value to pay'),
					],
					[
						'attribute' => 'calculation.issue.agent.fullName',
						'label' => Yii::t('issue', 'Agent'),
					],
					[
						'attribute' => 'calculation.issue.customer.fullName',
						'label' => Yii::t('issue', 'Customer'),
					],
					[
						'attribute' => 'calculation.issue.customer.profile.phone',
						'label' => 'Telefon [1]',
					],
					[
						'attribute' => 'calculation.issue.customer.profile.phone_2',
						'label' => 'Telefon [2]',
					],
					[
						'attribute' => 'calculation.issue.customer.email',
						'label' => 'Email',
					],

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
		$pay = $this->findModel($id);
		if ($pay->isPayed()) {
			Flash::add(
				Flash::TYPE_WARNING,
				Yii::t('settlement', 'The payment: {value} has already been paid.', [
					'value' => Yii::$app->formatter->asCurrency($pay->getValue()),
				]));

			return $this->redirect(Url::previous());
		}
		$model = new PayPayedForm($this->findModel($id));
		$model->date = date('Y-m-d');
		if ($model->load(Yii::$app->request->post()) && $model->pay()) {
			$generated = $model->getGeneratedPay();
			if ($generated !== null) {
				Flash::add(Flash::TYPE_WARNING,
					Yii::t('settlement', 'An incomplete: {value} has been paid.', [
						'value' => Yii::$app->formatter->asCurrency($model->value),
					])
				);
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('settlement', 'Generate new payment: {value}.', [
						'value' => Yii::$app->formatter->asCurrency($generated->getValue()),
					])
				);
				Yii::$app->provisions->removeForPays([$pay->calculation->getPays()->getIds(true)]);
				try {
					Yii::$app->provisions->settlement($pay->calculation);
				} catch (MissingProvisionUserException $exception) {

				}
			} else {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('settlement', 'The payment: {value} marked as paid.', [
						'value' => Yii::$app->formatter->asCurrency($pay->getValue()),
					]));
			}

			if ($model->pushMessages(Yii::$app->user->getId())) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('settlement', 'Send Messages about Payed Pay.')
				);
			}

			return $this->redirect(Url::previous());
		}
		return $this->render('pay', [
			'model' => $model,
		]);
	}

	public function actionUpdate(int $id) {
		$model = $this->findModel($id);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$dirty = $model->getDirtyAttributes();
			Yii::info([
				'message' => 'Update pay',
				'dirty' => $dirty,
				'user_id' => Yii::$app->user->id,
			], 'settlement.pay');
			if (isset($dirty['value'])) {
				Yii::$app->provisions->removeForPays($model->calculation->getPays()->getIds(true));
				try {
					Yii::$app->provisions->settlement($model->calculation);
				} catch (MissingProvisionUserException $exception) {

				}
			}

			return $this->redirect(Url::previous());
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionStatus(int $id) {
		$model = $this->findModel($id);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(Url::previous());
		}
		return $this->render('status', [
			'model' => $model,
		]);
	}

	public function actionDelete(int $id) {
		$model = $this->findModel($id);
		$model->delete();
		return $this->redirect(Url::previous());
	}

	/**
	 * Finds the IssuePay model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssuePay the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssuePay {
		if (($model = IssuePay::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

}
