<?php

namespace backend\modules\issue\controllers;

use backend\helpers\Url;
use backend\modules\issue\models\search\SummonSearch;
use backend\modules\issue\models\SummonForm;
use common\behaviors\IssueTypeParentIdAction;
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
					'realize' => ['POST'],
				],
			],
			'typeTypeParent' => [
				'class' => IssueTypeParentIdAction::class,
			],
		];
	}

	/**
	 * Lists all Summon models.
	 *
	 * @return mixed
	 */
	public function actionIndex(int $parentTypeId = null): string {
		$searchModel = new SummonSearch();
		$searchModel->issueParentTypeId = IssueTypeParentIdAction::validate($parentTypeId);
		if (!Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)
			&& isset($searchModel->getContractorsNames()[Yii::$app->user->getId()])) {
			$searchModel->contractor_id = Yii::$app->user->getId();
		}

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
		$model = $this->findModel($id);
		if (Yii::$app->request->isPjax) {
			return $this->renderAjax('_reminder-grid', [
				'model' => $model,
			]);
		}
		return $this->render('view', [
			'model' => $model,
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
		$model->start_at = date('Y-m-d');
		if ($typeId && isset(SummonType::getModels()[$typeId])) {
			$model->setType(SummonType::getModels()[$typeId]);
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$summon = $model->getModel();
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('issue', 'Create Summon - {type}: {title}', [
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

	public function actionCreateMultiple(int $typeId = null, string $returnUrl = null, array $ids = []) {
		$model = new SummonForm();
		if (empty($ids)) {
			$ids = IssueController::getSelectionSearchIds();
		}
		$model->issuesIds = $ids;

		$id = $ids[array_key_first($ids)];
		if (count($ids) === 1) {
			return $this->redirect('create', [
				'typeId' => $typeId,
				'returnUrl' => $returnUrl,
				'id' => $id,
			]);
		}
		$model->issue_id = $id;
		$model->owner_id = Yii::$app->user->id;
		$model->start_at = date('Y-m-d');
		if ($typeId && isset(SummonType::getModels()[$typeId])) {
			$model->setType(SummonType::getModels()[$typeId]);
		}

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$count = $model->createMultiple(false);
			if ($count) {
				Flash::add(Flash::TYPE_SUCCESS,
					Yii::t('backend', 'Success create Summons for Issues: {count}', [
						'count' => $count,
					]));
			}
			return $this->redirect($returnUrl ?: 'index');
		}
		return $this->render('create-multiple', [
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
		$summon = $this->findModel($id);
		if (!static::canUpdate($summon)) {
			throw new ForbiddenHttpException('Only for Owner or Summon Manager.');
		}
		$model->setModel($summon);
		$model->updater_id = Yii::$app->user->getId();
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
		$model = $this->findModel($id);
		if (static::canDelete($model)) {
			$model->delete();
		}

		return $this->redirect(['index']);
	}

	public function actionReminder(int $id) {
		$model = $this->findModel($id);
		if (!static::canUpdate($model)) {
			throw new ForbiddenHttpException();
		}
	}

	public function actionRealize(int $id, string $returnUrl = null) {
		$model = $this->findModel($id);
		if (!static::canUpdate($model)) {
			throw new ForbiddenHttpException();
		}
		$form = new SummonForm();
		$form->setModel($model);
		$form->status = Summon::STATUS_REALIZED;
		$form->updater_id = Yii::$app->user->getId();
		if ($form->save()) {
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('issue', 'Success. Mark Summon as Realized.')
			);
		}
		return $this->redirect($returnUrl
			? Url::to($returnUrl)
			: ['view', 'id' => $id]
		);
	}

	/**
	 * Finds the Summon model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return Summon the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): Summon {
		if (($model = Summon::findOne($id)) === null) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
		return $model;
	}

	public static function canUpdate(Summon $model): bool {
		return $model->isForUser(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER);
	}

	public static function canDelete(Summon $model): bool {
		return $model->isOwner(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER);
	}

}
