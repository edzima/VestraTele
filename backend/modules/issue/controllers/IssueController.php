<?php

namespace backend\modules\issue\controllers;

use backend\helpers\Url;
use backend\modules\issue\models\IssueForm;
use backend\modules\issue\models\IssueStageChangeForm;
use backend\modules\issue\models\search\IssueSearch;
use backend\modules\issue\models\search\SummonSearch;
use backend\modules\settlement\models\search\IssuePayCalculationSearch;
use backend\widgets\CsvForm;
use common\models\issue\Issue;
use common\models\issue\query\IssueQuery;
use common\models\user\Customer;
use common\models\user\Worker;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
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
			/** @var IssueQuery $query */
			$query = clone($dataProvider->query);
			$query->with('customer.userProfile');
			$query->with('type');
			$columns = [
				[
					'attribute' => 'longID',
					'label' => 'Nr',
				],
				[
					'attribute' => 'customer.fullName',
					'label' => 'Imie nazwisko',
				],
				[
					'attribute' => 'customer.userProfile.phone',
					'label' => 'Telefon [1]',
				],
				[
					'attribute' => 'customer.userProfile.phone_2',
					'label' => 'Telefon [2]',
				],
				[
					'attribute' => 'customer.email',
					'label' => 'Email',
				],
				[
					'attribute' => 'type.name',
					'label' => 'Typ',
				],
			];
			$addressSearch = $searchModel->addressSearch;
			if (!empty($addressSearch->region_id)
				|| !empty($addressSearch->city_name)
				|| !empty($addressSearch->postal_code)) {
				$query->with('customer.addresses.address.city.terc');
				$query->with('customer.addresses.address.city.terc.districts');

				$addressColumns = [
					[
						'attribute' => 'customer.homeAddress.city.region.name',
						'label' => Yii::t('address', 'Region'),
					],
					[
						'attribute' => 'customer.homeAddress.city.terc.district.name',
						'label' => Yii::t('address', 'District'),
					],
					[
						'attribute' => 'customer.homeAddress.city.terc.commune.name',
						'label' => Yii::t('address', 'Commune'),
					],
					[
						'attribute' => 'customer.homeAddress.postal_code',
						'label' => Yii::t('address', 'Code'),
					],
					[
						'attribute' => 'customer.homeAddress.city.name',
						'label' => Yii::t('address', 'City'),
					],
					[
						'attribute' => 'customer.homeAddress.info',
						'label' => Yii::t('address', 'Info'),
					],
				];
				$columns = array_merge($columns, $addressColumns);
			}
			$exporter = new CsvGrid([
				'query' => $query,
				'columns' => $columns,
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
		$search = new IssuePayCalculationSearch();
		$search->withArchive = true;
		$search->issue_id = $id;
		$calculationsDataProvider = $search->search([]);
		$summonDataProvider = (new SummonSearch(['issue_id' => $model->id]))->search([]);
		$summonDataProvider->sort = false;
		$summonDataProvider->pagination = false;

		return $this->render('view', [
			'model' => $model,
			'calculationsDataProvider' => $calculationsDataProvider,
			'summonDataProvider' => $summonDataProvider,
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
	public function actionUpdate(int $id) {
		$form = new IssueForm(['model' => $this->findModel($id)]);
		if ($form->load(Yii::$app->request->post()) && $form->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}
		return $this->render('update', [
			'model' => $form,
		]);
	}

	public function actionStage(int $issueId, int $stageId = null, string $returnUrl = null) {
		$model = new IssueStageChangeForm($this->findModel($issueId));
		if ($stageId !== null) {
			$model->stage_id = $stageId;
		}
		$model->date_at = date($model->dateFormat);
		$model->user_id = Yii::$app->user->getId();
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect($returnUrl ?? ['view', 'id' => $issueId]);
		}
		return $this->render('stage', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Issue model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete(int $id): Response {
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
	protected function findModel(int $id): Issue {
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
