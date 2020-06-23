<?php

namespace backend\modules\issue\controllers;

use backend\helpers\Url;
use backend\modules\issue\models\IssueProvisionUsersForm;
use backend\modules\issue\models\PayCalculationForm;
use common\models\issue\Issue;
use common\models\User;
use Yii;
use common\models\issue\IssuePayCalculation;
use backend\modules\issue\models\searches\IssuePayCalculationSearch;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PayCalculationController implements the CRUD actions for IssuePayCalculation model.
 */
class PayCalculationController extends Controller {

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
			'local-access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'roles' => [User::ROLE_BOOKKEEPER],
					],
					[
						'allow' => true,
						'actions' => ['view'],
						'roles' => [User::ROLE_BOOKKEEPER_DELAYED],
					],
				],
			],
		];
	}

	/**
	 * Lists all IssuePayCalculation models.
	 *
	 * @param bool $onlyNew
	 * @param int $status
	 * @return mixed
	 */
	public function actionIndex(int $status = IssuePayCalculationSearch::STATUS_ACTIVE, bool $onlyNew = false) {
		$searchModel = new IssuePayCalculationSearch(['isOnlyNew' => $onlyNew, 'status' => $status]);
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single IssuePayCalculation model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView(int $id) {
		$model = IssuePayCalculation::findOne($id);


		if ($model === null) {
			return $this->redirect(['create', 'id' => $id]);
		}

		return $this->render('view', [
			'model' => $model,
		]);
	}

	/**
	 * Create or Updates an existing IssuePayCalculation model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionCreate(int $id) {
		if (IssuePayCalculation::findOne($id) !== null) {
			return $this->redirect(['update', 'id' => $id]);
		}
		$issue = $this->findIssueModel($id);
		$model = new PayCalculationForm($issue);
		$provisionModel = new IssueProvisionUsersForm(['issue' => $issue]);
		if ($this->checkProvisions($issue)
			&& $model->load(Yii::$app->request->post())
			&& (!$model->isGenerate())
			&& $model->save()) {
			if ($provisionModel->load(Yii::$app->request->post()) && $provisionModel->save()) {
				return $this->redirect(['view', 'id' => $model->getPayCalculation()->issue_id]);
			}
		}

		return $this->render('create', [
			'model' => $model,
			'provisionModel' => $provisionModel,
		]);
	}

	private function checkProvisions(Issue $model): bool {
		$provisions = Yii::$app->provisions;
		if (empty($provisions->getTypes())) {
			Yii::$app->session->addFlash('error', 'Brakuje ustawionych typów prowizji');
			return false;
		}
		$hasAll = true;
		if (!$provisions->hasAllProvisions($model->lawyer)) {
			$hasAll = false;
			static::addUserProvisionFlash($model->lawyer);
		}
		if (!$provisions->hasAllProvisions($model->agent)) {
			$hasAll = false;
			static::addUserProvisionFlash($model->agent);
		}
		if ($model->hasTele() && !$provisions->hasAllProvisions($model->tele)) {
			$hasAll = false;
			static::addUserProvisionFlash($model->tele);
		}
		return $hasAll;
	}

	public static function addUserProvisionFlash(User $user): void {
		$link = Html::a($user, Url::userProvisions($user->id), ['target' => '_blank']);
		Yii::$app->session->addFlash('error', 'Brakuje prowizji dla: ' . $link);
	}

	/**
	 * Updates an existing IssuePayCalculation model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$issue = $this->findIssueModel($id);
		$model = new PayCalculationForm($issue);
		$provisionModel = new IssueProvisionUsersForm(['issue' => $issue]);
		$post = Yii::$app->request->post();
		if ($this->checkProvisions($issue)
			&& $model->load(Yii::$app->request->post())
			&& (!$model->isGenerate())
			&& $model->save()) {
			if ($provisionModel->load($post) && $provisionModel->save()) {
				return $this->redirect(['view', 'id' => $model->getPayCalculation()->issue_id]);
			}
		}

		return $this->render('update', [
			'model' => $model,
			'provisionModel' => $provisionModel,

		]);
	}

	/**
	 * Deletes an existing IssuePayCalculation model.
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
	 * Finds the IssuePayCalculation model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssuePayCalculation the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id): IssuePayCalculation {
		if (($model = IssuePayCalculation::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	/**
	 * @param int $id
	 * @return Issue
	 * @throws NotFoundHttpException
	 */
	protected function findIssueModel(int $id): Issue {
		if (($model = Issue::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
