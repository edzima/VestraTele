<?php

namespace frontend\controllers;

use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\settlement\PaysForm;
use common\models\user\Worker;
use frontend\helpers\Url;
use frontend\models\search\IssuePayCalculationSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class SettlementController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'permissions' => [Worker::PERMISSION_ISSUE],
					],
				],
			],
		];
	}

	public function actionIndex(): string {
		$searchModel = new IssuePayCalculationSearch();
		$ids = Yii::$app->userHierarchy->getAllChildesIds(Yii::$app->user->getId());
		$ids[] = Yii::$app->user->getId();
		$searchModel->issueUsersIds = $ids;

		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * @param int $id
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): string {
		Url::remember();

		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	public function actionPays(int $id) {
		if (!Yii::$app->user->can(Worker::PERMISSION_CALCULATION_PAYS)) {
			throw new ForbiddenHttpException();
		}

		$calculation = $this->findModel($id);
		if ($calculation->isPayed()) {
			Yii::$app->session->addFlash('Warning', 'Only in not payed calculation can be generate pays.');
			return $this->redirect(['view', 'id' => $id]);
		}
		$model = new PaysForm();
		$model->deadline_at = date($model->dateFormat, strtotime('last day of this month'));
		$model->value = $calculation->getValueToPay()->toFixed(2);
		$model->count = 2;
		$pay = $calculation->getPays()->one();
		if ($pay) {
			$model->vat = $pay->getVAT()->toFixed(2);
		} else {
			$model->vat = $calculation->issue->type->vat;
		}

		if ($model->load(Yii::$app->request->post())
			&& $model->validate()
			&& !$model->isGenerate()
		) {
			$calculation->unlinkAll('notPayedPays', true);
			$pays = $model->getPays();
			foreach ($pays as $pay) {
				$calculationPay = new IssuePay();
				$calculationPay->setPay($pay);
				$calculation->link('pays', $calculationPay);
			}
			return $this->redirect(['view', 'id' => $id]);
		}
		return $this->render('pays', [
			'calculation' => $calculation,
			'model' => $model,
		]);
	}

	/**
	 * @param int $id
	 * @return IssuePayCalculation
	 * @throws NotFoundHttpException
	 */
	private function findModel(int $id): IssuePayCalculation {
		$model = IssuePayCalculation::findOne($id);
		if ($model === null || !Yii::$app->user->canSeeIssue($model->issue)) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
