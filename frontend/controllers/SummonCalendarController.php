<?php

namespace frontend\controllers;

use common\models\issue\Summon;
use frontend\helpers\Html;
use frontend\models\ContactorSummonCalendarSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SummonCalendarController extends Controller {

	public function behaviors(): array {
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'update' => ['POST'],
				],
			],
		];
	}

	public function runAction($id, $params = []) {
		if ($id !== 'index') {
			$params = array_merge($_POST, $params);
		}
		return parent::runAction($id, $params);
	}

	public function actionIndex(): string {
		return $this->render('index');
	}

	public function actionList(string $start = null, string $end = null): Response {
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}
		$model = new ContactorSummonCalendarSearch();
		$model->owner_id = (int) Yii::$app->user->getId();
		$model->start = (string) $start;
		$model->end = (string) $end;
		$data = [];

		foreach ($model->search()->getModels() as $model) {
			$data[] = [
				'id' => $model->id,
				'url' => Url::to(['summon/view', 'id' => $model->id]),
				'is' => 'event',
				'title' => $this->getClientFullName($model)." - ".$model->title,
				'start' => $model->start_at,
				'end' => $model->start_at,
				'phone' => Html::encode($this->getPhone($model)),
				'statusId' => $model->status,
				'typeId' => $model->type,
				'tooltipContent' => $this->getClientFullName($model),
			];
		}

		return $this->asJson($data);
	}

	public function actionDeadline(string $start = null, string $end = null): Response {
		if ($start === null) {
			$start = date('Y-m-01');
		}
		if ($end === null) {
			$end = date('Y-m-t 23:59:59');
		}
		$model = new ContactorSummonCalendarSearch();

		$model->owner_id = (int) Yii::$app->user->getId();
		$model->start = (string) $start;
		$model->end = (string) $end;
		$data = [];

		foreach ($model->search()->getModels() as $model) {
			$data[] = [
				'id' => $model->id,
				'url' => Url::to(['summon/view', 'id' => $model->id]),
				'is' => 'deadline',
				'title' => $this->getClientFullName($model),
				'start' => $model->deadline_at,
				'end' => $model->deadline_at,
				'tooltipContent' => 'test',
			];
		}

		return $this->asJson($data);
	}

	private function getPhone(Summon $model): string {
		$phone = $model->contractor->getProfile()->phone;
		if ($phone) {
			return $phone;
		}
		return '';
	}

	private function getClientFullName(Summon $model): string {
		return $model->contractor->getFullName();
	}

	public function actionUpdate(int $id, string $date_at): Response {
		$model = $this->findModel($id);
		$model->start_at = $date_at;
		if ($model->save()) {
			return $this->asJson(['success' => true]);
		}
		return $this->asJson([
			'success' => false,
			'errors' => $model->getErrors(),
		]);
	}

	/**
	 * @param int $id
	 * @return Summon
	 * @throws NotFoundHttpException
	 */
	private function findModel(int $id): Summon {
		if (($model = Summon::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException();
	}

}
