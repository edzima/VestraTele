<?php

namespace frontend\controllers;

use backend\helpers\Url;
use common\helpers\Flash;
use common\models\issue\Issue;
use common\models\issue\Summon;
use common\models\issue\SummonType;
use common\models\user\Worker;
use frontend\models\search\SummonSearch;
use frontend\models\SummonForm;
use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
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
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'permissions' => [Worker::PERMISSION_SUMMON],
						'matchCallback' => function ($rule, Action $action): bool {
							if ($action->id === 'create') {
								return Yii::$app->user->can(Worker::PERMISSION_SUMMON_CREATE);
							}
							return true;
						},
					],
				],
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
		$searchModel->issueParentTypeId = $parentTypeId;
		$searchModel->user_id = Yii::$app->user->getId();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Creates a new Summon model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param int|null $issueId
	 * @return mixed
	 */
	public function actionCreate(int $issueId, int $typeId = null, string $returnUrl = null) {
		$issue = Issue::findOne($issueId);
		if ($issue === null || !Yii::$app->user->canSeeIssue($issue)) {
			throw new NotFoundHttpException();
		}
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
				Yii::t('issue', 'Create Summon - {type}: {title}', [
					'type' => $summon->type->name,
					'title' => $summon->title,
				]));
			$model->sendEmailToContractor();
			return $this->redirect($returnUrl ?? ['view', 'id' => $summon->id]);
		}

		return $this->render('create', [
			'issue' => $issue,
			'model' => $model,
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
	 * Updates an existing Summon model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException|MethodNotAllowedHttpException if the model cannot be found
	 */
	public function actionUpdate(int $id) {
		$summon = $this->findModel($id);
		if (!static::canUpdate($summon)) {
			throw new ForbiddenHttpException('Only for Owner or Summon Manager.');
		}
		$model = new SummonForm();
		$model->setModel($summon);
		$model->updater_id = Yii::$app->user->getId();
		if ($summon->owner_id !== Yii::$app->getUser()->getId()) {
			$model->scenario = SummonForm::SCENARIO_CONTRACTOR;
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->getModel()->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
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
		if (($model = Summon::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	public static function canUpdate(Summon $model): bool {
		return static::isOwnerOrSummonManager($model);
	}

	private static function isOwnerOrSummonManager(Summon $model): bool {
		return $model->isOwner(Yii::$app->user->getId()) || Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER);
	}
}
