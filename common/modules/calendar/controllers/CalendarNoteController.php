<?php

namespace common\modules\calendar\controllers;

use common\helpers\ArrayHelper;
use common\helpers\Html;
use common\models\CalendarNews;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

abstract class CalendarNoteController extends Controller {

	abstract protected function getType(): string;

	public bool $allowWithoutUser = false;
	public bool $alwaysWithoutUser = false;

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
					'create' => ['POST'],
					'update' => ['POST'],
					'delete' => ['POST'],
				],
			],
		];
	}

	public function runAction($id, $params = []) {
		$params = ArrayHelper::merge(Yii::$app->getRequest()->getBodyParams(), $params);
		return parent::runAction($id, $params);
	}

	public function actionList(string $start = null, string $end = null, int $userId = null): Response {
		if ($userId === null && !$this->allowWithoutUser) {
			$userId = (int) Yii::$app->user->getId();
		}
		if ($this->alwaysWithoutUser) {
			$userId = null;
		}
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}

		/** @var CalendarNews[] $models */
		$models = CalendarNews::find()
			->andFilterWhere(['user_id' => $userId])
			->andWhere(['>=', 'start_at', $start])
			->andWhere(['<=', 'end_at', $end])
			->andWhere(['type' => $this->getType()])
			->all();
		$data = [];
		foreach ($models as $model) {
			$data[] = $this->getListEventData($model);
		}

		return $this->asJson($data);
	}

	protected function getListEventData(CalendarNews $model): array {
		return [
			'id' => $model->id,
			'title' => Html::encode($model->text),
			'start' => $model->start_at,
			'end' => $model->end_at,
			'isNote' => true,
			'delete' => $this->allowDelete($model),
			'update' => $this->allowUpdate($model),
		];
	}

	public function actionCreate(string $news, string $date): Response {
		$model = new CalendarNews();
		$model->user_id = Yii::$app->user->getId();
		$model->text = $news;
		$model->start_at = $date;
		$model->end_at = $date;
		$model->type = $this->getType();
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
		if ($this->allowDelete($model)) {
			return $this->asJson([
				'success' => $model->delete(),
			]);
		}
		return $this->asJson([
			'success' => false,
		]);
	}

	protected function findModel(int $id): CalendarNews {
		$model = CalendarNews::find()
			->andWhere(['id' => $id])
			->andWhere(['type' => $this->getType()])
			->one();

		if ($model !== null) {
			return $model;
		}
		throw new NotFoundHttpException();
	}

	protected function allowDelete(CalendarNews $model): bool {
		return $model->user_id === Yii::$app->user->getId();
	}

	protected function allowUpdate(CalendarNews $model) {
		return $model->user_id === Yii::$app->user->getId();
	}

}
