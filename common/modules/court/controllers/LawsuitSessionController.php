<?php

namespace common\modules\court\controllers;

use common\modules\court\models\Lawsuit;
use common\modules\court\models\LawsuitSession;
use common\modules\court\models\LawsuitSessionForm;
use common\modules\court\models\search\LawsuitSessionSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * LawsuitSessionController implements the CRUD actions for LawsuitSession model.
 */
class LawsuitSessionController extends Controller {

	/**
	 * @inheritDoc
	 */
	public function behaviors(): array {
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::class,
					'actions' => [
						'delete' => ['POST'],
					],
				],
			]
		);
	}

	/**
	 * Lists all LawsuitSession models.
	 *
	 * @return string
	 */
	public function actionIndex(): string {
		$searchModel = new LawsuitSessionSearch();
		$dataProvider = $searchModel->search($this->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single LawsuitSession model.
	 *
	 * @param int $id ID
	 * @return string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new LawsuitSession model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return string|Response
	 */
	public function actionCreate(int $lawsuitId) {
		$lawsuit = Lawsuit::findOne($lawsuitId);
		if ($lawsuit === null) {
			throw new NotFoundHttpException();
		}
		$model = new LawsuitSessionForm();
		$model->setLawsuit($lawsuit);

		if ($this->request->isPost) {
			if ($model->load($this->request->post()) && $model->save()) {
				return $this->redirect(['lawsuit/view', 'id' => $lawsuitId]);
			}
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing LawsuitSession model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int $id ID
	 * @return string|Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new LawsuitSessionForm();
		$model->setModel($this->findModel($id));

		if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
			return $this->redirect(['lawsuit/view', 'id' => $model->lawsuit_id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing LawsuitSession model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param int $id ID
	 * @return Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$model = $this->findModel($id);
		$model->delete();

		return $this->redirect(['lawsuit/view', 'id' => $model->lawsuit_id]);
	}

	/**
	 * Finds the LawsuitSession model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param int $id ID
	 * @return LawsuitSession the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): LawsuitSession {
		if (($model = LawsuitSession::findOne(['id' => $id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('court', 'The requested page does not exist.'));
	}
}
