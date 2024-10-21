<?php

namespace frontend\controllers;

use common\components\provision\exception\Exception;
use common\helpers\Flash;
use common\models\issue\IssuePay;
use common\models\settlement\PayPayedForm;
use common\models\user\UserVisible;
use common\models\user\Worker;
use frontend\helpers\Url;
use frontend\models\search\IssuePaySearch;
use frontend\models\UpdatePayForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class PayController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'pay-provisions'],
						'roles' => [Worker::PERMISSION_ISSUE],
					],
					[
						'allow' => true,
						'actions' => ['update'],
						'roles' => [Worker::PERMISSION_PAY_UPDATE],
					],
					[
						'allow' => true,
						'actions' => ['pay'],
						'roles' => [Worker::PERMISSION_PAY_PAID],
					],
				],
			],
		];
	}

	public function actionIndex(string $status = IssuePaySearch::PAY_STATUS_NOT_PAYED): string {
		$searchModel = new IssuePaySearch();
		$searchModel->userId = Yii::$app->user->id;
		$searchModel->payStatus = $status;
		$userId = Yii::$app->user->getId();
		$ids = Yii::$app->userHierarchy->getAllChildesIds($userId);
		$ids[] = $userId;
		$ids = array_diff($ids, UserVisible::hiddenUsers(Yii::$app->user->getId()));

		$searchModel->agents_ids = $ids;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		Url::remember();
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionUpdate(int $id) {
		try {
			$model = new UpdatePayForm($this->findModel($id));
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
				return $this->goBack();
			}
			return $this->render('update', [
				'model' => $model,
			]);
		} catch (InvalidConfigException $exception) {
			throw new NotFoundHttpException($exception->getMessage());
		}
	}

	public function actionPayProvisions(): string {
		if (isset($_POST['expandRowKey'])) {
			$id = $_POST['expandRowKey'];
			$model = $this->findModel($id);

			$userId = Yii::$app->user->getId();
			if ($model->calculation->issue->isForUser($userId)
				|| $model->calculation->issue->isForAgents(Yii::$app->userHierarchy->getAllChildesIds($userId))
			) {
				$query = $model->getProvisions()
					->joinWith('toUser.userProfile')
					->joinWith('fromUser.userProfile')
					->user($userId);

				$dataProvider = new ActiveDataProvider([
					'query' => $query,
				]);
				return $this->renderPartial('_pay_provisions', [
					'dataProvider' => $dataProvider,
				]);
			}
		}
		return '<div class="alert alert-danger">No data found</div>';
	}

	public function actionPay(int $id) {
		$pay = $this->findModel($id);
		if ($pay->isPayed()) {
			Flash::add(
				Flash::TYPE_WARNING,
				Yii::t('settlement', 'The payment: {value} has already been paid.', [
					'value' => Yii::$app->formatter->asCurrency($pay->getValue()),
				]));

			return $this->redirect(\backend\helpers\Url::previous());
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
				Yii::$app->provisions->removeForPays($pay->calculation->getPays()->getIds(true));
				try {
					Yii::$app->provisions->settlement($pay->calculation);
				} catch (Exception $exception) {

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

	private function findModel(int $id): IssuePay {
		$model = IssuePay::findOne($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		if (!$model->calculation->type->hasAccess(Yii::$app->user->getId())) {
			throw new ForbiddenHttpException();
		}

		return $model;
	}
}
