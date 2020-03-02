<?php

namespace frontend\controllers;

use common\models\issue\IssueMeet;
use common\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MeetCalendarController extends Controller {

	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'update' => ['POST'],
				],
			],
		];
	}

	public function runAction($id, $params = []) {
		$params = array_merge($_POST, $params);
		parent::runAction($id, $params);
	}

	public function actionCalendar() {
		/** @var User $user */
		$user = Yii::$app->user->getIdentity();
		$ids = array_merge([$user->id], $user->getAllChildesIds());

		$agents = ArrayHelper::map(User::find()
			->andWhere(['id' => $ids])
			->with('userProfile')
			->all(), 'id', 'fullName');

		return $this->render('calendar', ['agents' => $agents]);
	}

	public function actionList(string $dateFrom = null, string $dateTo = null, int $agentId = null): Response {
		if ($agentId === null) {
			$agentId = Yii::$app->user->getId();
		}
		if ($dateFrom === null) {
			$dateFrom = date('Y-m-01');
		}
		if ($dateTo === null) {
			$dateTo = date('Y-m-t 23:59:59');
		}
		$models = IssueMeet::find()
			->andWhere(['agent_id' => $agentId])
			->andWhere(['>=', 'date_at', $dateFrom])
			->andWhere(['<=', 'date_at', $dateTo])
			->with('city')
			->all();

		$data = [];

		foreach ($models as $model) {
			$data[] = [
				'id' => $model->id,
				'typeId' => $model->type_id,
				'client' => $model->getClientFullName(),
				'city' => $model->city->name,
				'street' => $model->street,
				'state' => $model->state->name,
				'province' => $model->province->name,
				'subProvince' => $model->subProvince->name,
				'phone' => $model->phone,
				'date_at' => $model->date_at,
				'date_end_at' => $model->date_end_at,
				'details' => $model->details,
			];
		}

		return $this->asJson([
			'data' => $data,
		]);
	}

	public function actionUpdate(int $id, string $date_at, string $date_end_at): Response {
		$model = $this->findModel($id);
		$model->date_at = $date_at;
		$model->date_end_at = $date_end_at;
		return $this->asJson(['success' => $model->save()]);
	}

	/**
	 * @param int $id
	 * @return IssueMeet
	 * @throws NotFoundHttpException
	 */
	private function findModel(int $id): IssueMeet {
		if (($model = IssueMeet::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException();
	}

}
