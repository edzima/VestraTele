<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\MeetForm;
use backend\widgets\CsvForm;
use common\models\issue\IssueMeet;
use common\models\issue\IssueMeetSearch;
use common\models\user\Worker;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii2tech\csvgrid\CsvGrid;

/**
 * MeetController implements the CRUD actions for IssueMeet model.
 */
class MeetController extends Controller {

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
	 * Lists all IssueMeet models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new IssueMeetSearch();
		if (Yii::$app->user->can(Worker::PERMISSION_ARCHIVE)) {
			$searchModel->withArchive = true;
		}
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		if (isset($_POST[CsvForm::BUTTON_NAME])) {
			$query = clone $dataProvider->query;
			$columns = [
				[
					'attribute' => 'clientFullName',
					'label' => 'Nazwa',
				],
				['attribute' => 'phone'],
				['attribute' => 'email'],
			];
			$addressSearch = $searchModel->getAddressSearch();
			if (!empty($addressSearch->region_id)
				|| !empty($addressSearch->city_name)
				|| !empty($addressSearch->postal_code)) {
				$query->joinWith('addresses.address.city.terc');

				$addressColumns = [
					[
						'attribute' => 'customerAddress.city.region.name',
						'label' => Yii::t('address', 'Region'),
					],
					[
						'attribute' => 'customerAddress.city.terc.district.name',
						'label' => Yii::t('address', 'District'),
					],
					[
						'attribute' => 'customerAddress.city.terc.commune.name',
						'label' => Yii::t('address', 'Commune'),
					],
					[
						'attribute' => 'customerAddress.postal_code',
						'label' => Yii::t('address', 'Code'),
					],
					[
						'attribute' => 'customerAddress.city.name',
						'label' => Yii::t('address', 'City'),
					],
					[
						'attribute' => 'customerAddress.info',
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
	 * Displays a single IssueMeet model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($id) {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new IssueMeet model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new MeetForm();

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing IssueMeet model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id) {

		$model = new MeetForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing IssueMeet model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the IssueMeet model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return IssueMeet the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id): IssueMeet {
		if (($model = IssueMeet::findOne($id)) !== null) {
			if ($model->isArchived() && !Yii::$app->user->can(Worker::PERMISSION_ARCHIVE)) {
				Yii::warning('User: ' . Yii::$app->user->id . ' try view archived meet: ' . $model->id, 'meet');

				throw new MethodNotAllowedHttpException('Spotkanie jest w archiwum.');
			}
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
