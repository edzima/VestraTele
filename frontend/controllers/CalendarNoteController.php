<?php

namespace frontend\controllers;

use common\models\CalendarNews;
use common\models\user\User;
use frontend\helpers\Html;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;

class CalendarNoteController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		$this->enableCsrfValidation = YII_ENV_PROD;
		return parent::beforeAction($action);
	}

	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'add' => ['POST'],
					'update' => ['POST'],
					'delete' => ['POST'],
				],
			],
		];
	}

	public function runAction($id, $params = []) {
		$params = array_merge($_POST, $params);
		return parent::runAction($id, $params);
	}

	public function actionList(string $start = null, string $end = null, int $userId = null): Response {
		if ($userId === null) {
			$userId = (int) Yii::$app->user->getId();
		}
		if ($userId !== (int) Yii::$app->user->getId() && !Yii::$app->user->can(User::ROLE_MANAGER)) {
			throw new MethodNotAllowedHttpException();
		}
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}
		/** @var CalendarNews[] $models */
		$models = CalendarNews::find()
			->andWhere(['user_id' => $userId])
			->andWhere(['>=', 'start_at', $start])
			->andWhere(['<=', 'end_at', $end])
			->all();
		$data = [];
		foreach ($models as $model) {
			$data[] = [
				'id' => $model->id,
				'title' => Html::encode($model->text),
				'start' => $model->start_at,
				'end' => $model->end_at,
				'isNote' => true,
			];
		}

		return $this->asJson($data);
	}

	public function actionAdd(string $news, string $date): Response {
		$model = new CalendarNews();
		$model->user_id = Yii::$app->user->getId();
		$model->text = $news;
		$model->start_at = $date;
		$model->end_at = $date;
		if ($model->save()) {
			return $this->asJson([
				'id' => $model->id,
			]);
		}
		return $this->asJson(['errors' => $model->getErrors()]);
	}

	public function actionUpdate(int $id, ?string $start_at = null, ?string $end_at = null, ?string $news = null): Response {
		$model = $this->findModel($id);
		if ($start_at !== null) {
			$model->start_at = $start_at;
		}
		if ($end_at !== null) {
			$model->end_at = $end_at;
		}
		if ($news !== null) {
			$model->text = $news;
		}

		if ($model->save()) {
			return $this->asJson(['success' => true]);
		}
		return $this->asJson([
			'errors' => $model->getErrors(),
		]);
	}

	public function actionDelete(int $id): Response {
		$model = $this->findModel($id);
		return $this->asJson([
			'success' => $model->delete(),
		]);
	}

	private function findModel(int $id): CalendarNews {
		$model = CalendarNews::findOne($id);
		if ($model !== null) {
			return $model;
		}
		throw new ForbiddenHttpException();
	}

}
