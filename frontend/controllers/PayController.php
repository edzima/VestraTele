<?php

namespace frontend\controllers;

use common\models\issue\IssuePay;
use common\models\user\User;
use common\models\user\Worker;
use frontend\helpers\Url;
use frontend\models\search\IssuePaySearch;
use frontend\models\UpdatePayForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PayController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [Worker::PERMISSION_ISSUE],
					],
				],
			],
		];
	}

	public function actionIndex(string $status = IssuePaySearch::PAY_STATUS_NOT_PAYED): string {
		$searchModel = new IssuePaySearch();
		$searchModel->payStatus = $status;
		$userId = Yii::$app->user->getId();
		$ids = Yii::$app->userHierarchy->getAllChildesIds($userId);
		$ids[] = $userId;

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
				||$model->calculation->issue->isForAgents(Yii::$app->userHierarchy->getAllChildesIds($userId))
			){
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

	private function findModel(int $id): IssuePay {
		$model = IssuePay::findOne($id);
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
