<?php

namespace backend\modules\provision\controllers;

use backend\helpers\Url;
use backend\modules\provision\models\ProvisionTypeForm;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSettlement;
use common\models\issue\IssueUser;
use common\models\provision\IssueProvisionType;
use common\models\provision\ProvisionTypeSearch;
use common\models\provision\ProvisionUser;
use common\models\provision\ProvisionUserSearch;
use common\models\user\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TypeController implements the CRUD actions for ProvisionType model.
 */
class TypeController extends Controller {

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
	 * Lists all ProvisionType models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$searchModel = new ProvisionTypeSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionSettlement(int $id): string {
		$model = $this->findSettlement($id);
		$searchModel = new ProvisionTypeSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->setModels(IssueProvisionType::settlementFilter($dataProvider->getModels(), $model));

		return $this->render('settlement', [
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single ProvisionType model.
	 *
	 * @param integer $id
	 * @return string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		$model = $this->findModel($id);

		$userWithTypesSearch = new ProvisionUserSearch();
		$userWithTypesSearch->type_id = $id;
		$userWithTypes = $userWithTypesSearch->search([]);

		if ($userWithTypes->getTotalCount() > 0) {
			$userIds = IssueUser::find()
				->withType($model->getIssueUserType())
				->select('user_id')
				->distinct()
				->leftJoin(ProvisionUser::tableName(), 'user_id = from_user_id AND user_id = to_user_id')
				->andWhere(['from_user_id' => null])
				->column();
		} else {
			$userIds = IssueUser::userIds($model->getIssueUserType());
		}

		$withoutType = new ActiveDataProvider([
			'query' => User::find()
				->joinWith('userProfile P')
				->orderBy('P.lastname')
				->active()
				->andWhere(['id' => $userIds]),
			'pagination' => false,
		]);

		Url::remember();

		return $this->render('view', [
			'model' => $model,
			'userWithTypes' => $userWithTypes,
			'withoutType' => $withoutType,
		]);
	}

	/**
	 * Creates a new ProvisionType model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new ProvisionTypeForm();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionCreateSettlement(int $id, string $issueUserType = null) {
		$calclulation = $this->findSettlement($id);
		$model = new ProvisionTypeForm();
		$model->issueUserType = $issueUserType;
		$model->issueTypesIds = [$calclulation->issue->type_id];
		$model->settlementTypes = [$calclulation->type];
		$model->name = $calclulation->getTypeName() . ' - ' . $calclulation->issue->type->name;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing ProvisionType model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new ProvisionTypeForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing ProvisionType model.
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
	 * Finds the ProvisionType model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueProvisionType the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): IssueProvisionType {
		if (($model = IssueProvisionType::getType($id, false)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	private function findSettlement(int $id): IssueSettlement {
		if (($model = IssuePayCalculation::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
