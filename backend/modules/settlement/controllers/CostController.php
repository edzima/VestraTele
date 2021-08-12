<?php

namespace backend\modules\settlement\controllers;

use backend\helpers\Url;
use backend\modules\issue\models\search\IssueSearch;
use backend\modules\settlement\models\DebtCostsForm;
use backend\modules\settlement\models\IssueCostForm;
use backend\modules\settlement\models\search\IssueCostSearch;
use backend\widgets\IssueColumn;
use common\helpers\Flash;
use common\models\issue\Issue;
use common\models\issue\IssueCost;
use common\models\issue\IssuePayCalculation;
use common\models\user\User;
use common\models\user\Worker;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii2tech\csvgrid\CsvGrid;

/**
 * CostController implements the CRUD actions for IssueCost model.
 */
class CostController extends Controller {

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'settlement-link' => ['POST'],
					'settlement-unlink' => ['POST'],
				],
			],
		];
	}

	public function actionPccExport() {
		$searchModel = new IssueCostSearch();
		$searchModel->type = IssueCost::TYPE_PCC;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		if ($dataProvider->getTotalCount()) {
			$exporter = new CsvGrid([
				'dataProvider' => $dataProvider,
				'columns' => [
					[
						'attribute' => 'issue.longId',
						'label' => Yii::t('common', 'Issue'),
					],
					[
						'attribute' => 'user',
						'label' => Yii::t('common', 'Full name'),
					],
					[
						'attribute' => 'value',
						'label' => Yii::t('settlement', 'Value'),
						'format' => 'currency',
					],
					[
						'attribute' => 'base_value',
						'label' => Yii::t('settlement', 'Nominal Value'),
						'format' => 'currency',
					],
					[
						'attribute' => 'date_at',
						'format' => 'date',
					],
					[
						'attribute' => 'deadline_at',
						'format' => 'date',
					],
					[
						'attribute' => 'settled_at',
						'format' => 'date',
					],
				],
			]);
			return $exporter->export()->send('export.csv');
		}
		Flash::add(Flash::TYPE_WARNING, Yii::t('settlement', 'Not found Costs for this filters.'));
		return $this->redirect(['index', Yii::$app->request->queryParams]);
	}

	public function actionPitExport() {
		$searchModel = new IssueCostSearch();
		$searchModel->type = IssueCost::TYPE_PIT_4;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		if ($dataProvider->getTotalCount()) {
			$exporter = new CsvGrid([
				'dataProvider' => $dataProvider,
				'columns' => [
					//PIT4 - numer sprawy, imię i nazwisko, PESEL, adres zamieszkania, Urząd skarbowy, data kosztu, data płatności, termin, wartość kosztu, wartość bazowa

					[
						'attribute' => 'issue.longId',
						'label' => Yii::t('common', 'Issue'),
					],
					[
						'attribute' => 'user',
						'label' => Yii::t('common', 'Full name'),
					],
					[
						'attribute' => 'user.userProfile.pesel',
						'label' => Yii::t('common', 'PESEL'),
					],
					[
						'attribute' => 'user.homeAddress',
						'label' => Yii::t('common', 'Home address'),
					],
					[
						'attribute' => 'user.userProfile.tax_office',
						'label' => Yii::t('settlement', 'Tax Office'),
					],
					[
						'attribute' => 'value',
						'label' => Yii::t('settlement', 'Value'),
						'format' => 'currency',
					],
					[
						'attribute' => 'base_value',
						'label' => Yii::t('settlement', 'Nominal Value'),
						'format' => 'currency',
					],
					[
						'attribute' => 'date_at',
						'format' => 'date',
					],
					[
						'attribute' => 'deadline_at',
						'format' => 'date',
					],
					[
						'attribute' => 'settled_at',
						'format' => 'date',
					],
				],
			]);
			return $exporter->export()->send('export.csv');
		}
		Flash::add(Flash::TYPE_WARNING, Yii::t('settlement', 'Not found Costs for this filters.'));
		return $this->redirect(['index', Yii::$app->request->queryParams]);
	}

	/**
	 * Lists all IssueCost models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$model = new IssueCostSearch();
		$dataProvider = $model->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Lists all IssueCost models for Issue.
	 *
	 * @param int $id
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionIssue(int $id): string {
		$issue = $this->findIssue($id);
		$model = new IssueCostSearch();
		$model->issue_id = $id;
		$dataProvider = $model->search(Yii::$app->request->queryParams);

		return $this->render('issue', [
			'model' => $model,
			'issue' => $issue,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Create Debt Costs for Issue
	 *
	 * @param int $issue_id
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionCreateDebt(int $issue_id) {
		$model = new DebtCostsForm($this->findIssue($issue_id));
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['issue', 'id' => $issue_id]);
		}
		return $this->render('create-debt', [
			'model' => $model,
		]);
	}

	public function actionSettlementLink(int $id, int $settlementId) {
		$model = $this->findModel($id);
		$settlement = IssuePayCalculation::findOne($settlementId);
		if ($settlement) {
			$model->link('settlements', $settlement);
		}
		return $this->redirect(Url::previous());
	}

	public function actionSettlementUnlink(int $id, int $settlementId) {
		$model = $this->findModel($id);
		$settlement = IssuePayCalculation::findOne($settlementId);
		if ($settlement) {
			$model->unlinkSettlement($settlementId);
		}
		return $this->redirect(Url::previous());
	}

	/**
	 * Displays a single IssueCost model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new IssueCost model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function actionCreate(int $id) {
		$issue = $this->findIssue($id);
		$model = new IssueCostForm($issue);
		$model->date_at = date(DATE_ATOM);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionCreateInstallment(int $id, int $user_id = null) {
		$issue = $this->findIssue($id);
		$model = new IssueCostForm($issue);
		$model->setScenario(IssueCostForm::SCENARIO_CREATE_INSTALLMENT);
		$model->user_id = $user_id;
		$model->type = IssueCost::TYPE_INSTALLMENT;
		$model->date_at = date(DATE_ATOM);
		$model->vat = 0;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create-installment', [
			'model' => $model,
		]);
	}

	public function actionSettle(int $id, string $redirectUrl = null) {
		$cost = $this->findModel($id);
		if ($cost->getIsSettled()) {
			Flash::add(
				Flash::TYPE_WARNING,
				Yii::t('settlement', 'Warning! Try settle already settled Cost.')
			);
			return $this->redirect('index');
		}
		$model = IssueCostForm::createFromModel($cost);
		$model->setScenario(IssueCostForm::SCENARIO_SETTLE);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect($redirectUrl ?? 'index');
		}

		return $this->render('settle', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueCost model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$cost = $this->findModel($id);
		$model = IssueCostForm::createFromModel($cost);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueCost model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the IssueCost model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueCost the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssueCost {
		if (($model = IssueCost::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	/**
	 * @param int $id
	 * @return Issue
	 * @throws NotFoundHttpException
	 */
	protected function findIssue(int $id): Issue {
		$model = Issue::find()->andWhere(['id' => $id]);
		if (!Yii::$app->user->can(User::PERMISSION_ARCHIVE)) {
			$model->withoutArchives();
		}
		$model = $model->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		return $model;
	}
}
