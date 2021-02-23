<?php

namespace backend\modules\provision\controllers;

use backend\helpers\Url;
use backend\modules\provision\models\ProvisionUserData;
use backend\modules\provision\models\ProvisionUserForm;
use common\helpers\Flash;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\provision\ProvisionUserSearch;
use common\models\user\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for ProvisionUser model.
 */
class UserController extends Controller {

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
	 * Lists all ProvisionUser models.
	 *
	 * @return mixed
	 */
	public function actionIndex(): string {
		$searchModel = new ProvisionUserSearch();
		$searchModel->onlySelf = true;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		Url::remember();

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * @param int $userId
	 * @param int|null $typeId
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionUserView(int $userId, int $typeId = null): string {
		$model = new ProvisionUserData($this->findUser($userId));
		if ($typeId !== null) {
			$model->type = $this->findType($typeId);
		}
		Url::remember();

		$fromDataProvider = new ActiveDataProvider([
			'query' => $model->getFromQuery()->with('fromUser.userProfile', 'type'),
			'pagination' => false,
		]);

		$toDataProvider = new ActiveDataProvider([
			'query' => $model->getToQuery()->with('toUser.userProfile', 'type'),
			'pagination' => false,
		]);

		$parentsWithoutProvisionsDataProvider = null;
		$parentsQuery = $model->getAllParentsQueryWithoutProvision();

		if ($parentsQuery) {
			$parentsWithoutProvisionsDataProvider = new ActiveDataProvider([
					'query' => $parentsQuery->orderByLastname(),
					'pagination' => false,
				]
			);
		}

		$allChildesDataProvider = null;
		$allChildesQuery = $model->getAllChildesQueryWithoutProvision(ArrayHelper::map($toDataProvider->getModels(), 'to_user_id', 'to_user_id'));
		if ($allChildesQuery) {
			$allChildesDataProvider = new ActiveDataProvider([
				'query' => $allChildesQuery->orderByLastname(),
				'pagination' => false,
			]);
		}

		if ($parentsWithoutProvisionsDataProvider !== null && $parentsWithoutProvisionsDataProvider->getTotalCount() > 0) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('provision',
					'There {n,plural, =1{parent} other{# parents}} has not set schema provisions!',
					['n' => $parentsWithoutProvisionsDataProvider->getTotalCount()]
				)
			);
		}

		if ($allChildesDataProvider !== null && $allChildesDataProvider->getTotalCount() > 0) {
			Flash::add(Flash::TYPE_WARNING,
				Yii::t('provision',
					'There {n,plural, =1{subordinate} other{# subordinates}} has not set schema provisions!',
					['n' => $allChildesDataProvider->getTotalCount()]
				)
			);
		}

		return $this->render('user-view', [
			'model' => $model,
			'selfDataProvider' => new ActiveDataProvider([
				'query' => $model->getSelfQuery()->with('type'),
				'pagination' => false,
			]),
			'fromDataProvider' => $fromDataProvider,
			'parentsWithoutProvisionsDataProvider' => $parentsWithoutProvisionsDataProvider,
			'toDataProvider' => $toDataProvider,
			'allChildesDataProvider' => $allChildesDataProvider,
		]);
	}

	/**
	 * @param int|null $typeId
	 * @param int|null $fromUserId
	 * @param int|null $toUserId
	 * @return string|Response
	 * @throws NotFoundHttpException if type not exist.
	 */
	public function actionCreate(int $typeId = null, int $fromUserId = null, int $toUserId = null) {
		$model = new ProvisionUserForm();
		$model->from_user_id = $fromUserId;
		$model->to_user_id = $toUserId;

		if ($typeId !== null) {
			$model->setType($this->findType($typeId));
		}
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(Url::previous());
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionCreateSelf(int $userId, int $typeId = null) {
		$user = $this->findUser($userId);
		$model = ProvisionUserForm::createUserSelfForm($userId);

		if ($typeId) {
			$model->setType($this->findType($typeId));
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(Url::previous());
		}

		return $this->render('create-self', [
			'user' => $user,
			'model' => $model,
		]);
	}

	public function actionUpdate(int $id) {
		$model = new ProvisionUserForm();
		$model->setModel($this->findModel($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(Url::previous());
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Deletes an existing ProvisionUser model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete(int $id) {
		$model = $this->findModel($id);
		$model->delete();
		return $this->redirect(Url::previous());
	}

	/**
	 * Finds the ProvisionUser model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 * @return ProvisionUser the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(int $id): ProvisionUser {
		if (($model = ProvisionUser::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	private function findType(int $typeId): ProvisionType {
		$model = ProvisionType::getTypes()[$typeId] ?? null;
		if ($model !== null) {
			return $model;
		}
		throw new NotFoundHttpException('The requested page does not exist.');
	}

	private function findUser(int $id): User {
		$user = User::findOne($id);
		if ($user === null) {
			throw new NotFoundHttpException();
		}
		return $user;
	}

}
