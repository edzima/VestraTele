<?php

namespace frontend\controllers;

use common\models\issue\IssueMeet;
use common\models\User;
use frontend\models\AgentMeetSearch;
use frontend\models\IssueMeetSearch;
use frontend\models\TeleMeetSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class MeetController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['all', 'create'],
						'roles' => [User::ROLE_MEET, User::ROLE_TELEMARKETER],
					],
					[
						'allow' => true,
						'actions' => ['tele'],
						'roles' => [User::ROLE_TELEMARKETER],
					],
					[
						'allow' => true,
						'actions' => ['agent'],
						'roles' => [User::ROLE_AGENT],
					],
					[
						'allow' => true,
						'actions' => ['index', 'view', 'update'],
						'roles' => [User::ROLE_MEET, User::ROLE_AGENT, User::ROLE_TELEMARKETER],
					],
				],
			],
		];
	}

	public function actionIndex() {
		$user = Yii::$app->user;
		if ($user->can(User::ROLE_MEET)) {
			return $this->redirect('all');
		}
		if ($user->can(User::ROLE_AGENT)) {
			return $this->redirect('agent');
		}
		if ($user->can(User::ROLE_TELEMARKETER)) {
			return $this->redirect('tele');
		}

		throw new ForbiddenHttpException();
	}

	public function actionAll(): string {
		$searchModel = new IssueMeetSearch();
		$data = Yii::$app->request->queryParams;
		if ($this->isMaciej()) {
			$data[$searchModel->formName()]['campaign_id'] = 1;
		}
		$dataProvider = $searchModel->search($data);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	private function isMaciej(): bool {
		return Yii::$app->user->getId() === User::MACIEJ_ID;
	}

	public function actionTele(): string {
		$user = Yii::$app->user;
		$searchModel = new TeleMeetSearch();
		$searchModel->tele_id = $user->id;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionAgent(): string {
		$user = Yii::$app->user;
		$searchModel = new AgentMeetSearch();
		$searchModel->agent_id = $user->id;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreate() {
		$user = Yii::$app->user;

		if (!$user->can(User::ROLE_TELEMARKETER)) {
			throw new NotFoundHttpException();
		}
		$model = new IssueMeet();
		$model->tele_id = $user->id;

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionUpdate(int $id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionView(int $id): string {
		return $this->render('view', [
			'model' => $this->findModel($id, !$this->isMaciej()),
		]);
	}

	protected function findModel(int $id, bool $userFilter = true): IssueMeet {
		$user = Yii::$app->user;

		$query = IssueMeet::find();
		$query->andWhere(['id' => $id]);

		if ($userFilter && !Yii::$app->user->can(User::ROLE_MEET)) {
			$query->andWhere([
				'or',
				['tele_id' => $user->getId()],
				['agent_id' => $user->getId()],
			]);
		}

		$model = $query->one();
		if ($model === null) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}

		return $model;
	}

}
