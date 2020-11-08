<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\IssueForm;
use backend\modules\issue\models\search\IssueSearch;
use backend\widgets\CsvForm;
use common\models\issue\Issue;
use common\models\user\Customer;
use common\models\user\Worker;
use Yii;
use yii\db\ActiveQuery;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii2tech\csvgrid\CsvGrid;

/**
 * IssueController implements the CRUD actions for Issue model.
 */
class IssueController extends Controller {

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
		];
	}

	/**
	 * Lists all Issue models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {

		$searchModel = new IssueSearch();
		if (Yii::$app->user->can(Worker::PERMISSION_ARCHIVE)) {
			$searchModel->withArchive = true;
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		if (isset($_POST[CsvForm::BUTTON_NAME])) {
			/** @var ActiveQuery $query */
			$query = clone($dataProvider->query);
			$query->with('clientCity');
			$query->with('clientProvince');
			$query->with('clientState');
			$exporter = new CsvGrid([
				'query' => $query,
				'columns' => [
					[
						'attribute' => 'clientFullName',
						'label' => 'Nazwa',
					],
					[
						'attribute' => 'client_street',
						'label' => 'Ulica',
					],
					[
						'attribute' => 'client_phone_1',
						'label' => 'Telefon',
					],
					[
						'attribute' => 'clientCity.name',
						'label' => 'Miasto',
					],
					[
						'attribute' => 'clientProvince.name',
						'label' => 'Powiat',
					],
					[
						'attribute' => 'clientState.name',
						'label' => 'Województwo',
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

	/**
	 * Displays a single Issue model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView(int $id): string {
		$model = $this->findModel($id);
		return $this->render('view', [
			'model' => $model,
		]);
	}

	/**
	 * Creates a new Issue model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $customerId
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function actionCreate(int $customerId) {
		$customer = Customer::findOne($customerId);
		if ($customer === null) {
			throw new NotFoundHttpException('Client not exist');
		}

		$model = new IssueForm(['customer' => $customer]);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Issue model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$form = new IssueForm(['model' => $this->findModel($id)]);
		if ($form->load(Yii::$app->request->post()) && $form->save()) {
			return $this->redirect(['index']);
		}
		return $this->render('update', [
			'model' => $form,
		]);
	}

	/**
	 * Deletes an existing Issue model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the Issue model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Issue the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id): Issue {
		if (($model = Issue::findOne($id)) !== null) {

			if ($model->isArchived() && !Yii::$app->user->can(Worker::PERMISSION_ARCHIVE)) {
				Yii::warning('User: ' . Yii::$app->user->id . ' try view archived issue: ' . $model->id, 'issue');

				throw new MethodNotAllowedHttpException('Sprawa jest w archiwum.');
			}
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
