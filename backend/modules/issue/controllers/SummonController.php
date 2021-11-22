<?php

namespace backend\modules\issue\controllers;

use backend\modules\issue\models\search\SummonSearch;
use backend\modules\issue\models\SummonForm;
use common\helpers\Flash;
use common\models\issue\Summon;
use common\models\issue\SummonType;
use common\models\user\Worker;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

/**
 * SummonController implements the CRUD actions for Summon model.
 */
class SummonController extends Controller {

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
	 * Lists all Summon models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new SummonSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Summon model.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id, false),
		]);
	}

	/**
	 * Creates a new Summon model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int|null $issueId
	 * @return mixed
	 */
	public function actionCreate(int $issueId = null, int $typeId = null, string $returnUrl = null) {
		$model = new SummonForm();
		$model->owner_id = Yii::$app->user->id;
		$model->issue_id = $issueId;
		$model->start_at = time();
		if ($typeId && isset(SummonType::getModels()[$typeId])) {
			$model->setType(SummonType::getModels()[$typeId]);
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$summon = $model->getModel();
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('backend', 'Create Summon - {type}: {title}', [
					'type' => $summon->type->name,
					'title' => $summon->title,
				]));
			$model->sendEmailToContractor();
			return $this->redirect($returnUrl ?? ['view', 'id' => $summon->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Summon model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException|MethodNotAllowedHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$model = new SummonForm();
		$model->setModel($this->findModel($id, true));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing Summon model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$this->findModel($id, true)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the Summon model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Summon the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 * @throws ForbiddenHttpException if the model is not for User.
	 */
	protected function findModel(int $id, bool $checkUser): Summon {
		if (($model = Summon::findOne($id)) === null) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		if ($checkUser) {

			if (!$model->isForUser(Yii::$app->user->getId())
				&& !Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
				throw new ForbiddenHttpException('Only User or Summon Manager can update.');
			}
		}

		return $model;
	}
}
