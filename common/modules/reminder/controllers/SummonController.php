<?php

namespace common\modules\reminder\controllers;

use common\models\issue\Summon;
use common\models\issue\SummonReminder;
use common\models\issue\SummonReminderForm;
use common\models\user\Worker;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class SummonController extends Controller {

	public function behaviors(): array {
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
					'done' => ['POST'],
					'not-done' => ['POST'],
				],
			],
			[
				'class' => AjaxFilter::class,
				'only' => ['create', 'update'],
			],
		];
	}

	public string $summonView = '/issue/summon/view';

	public function actionCreate(int $id) {
		$model = new SummonReminderForm();
		$model->user_id = Yii::$app->user->getId();
		$model->setSummon($this->findSummon($id));

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->asJson([
				'success' => true,
			]);
		}
		if ($model->hasErrors()) {
			return $this->asJson([
				'errors' => $model->getErrors(),
				'usersIds' => $model->usersRange,

			]);
		}
		return $this->renderAjax('form', [
			'model' => $model,
		]);
	}

	public function actionView(int $lead_id) {
		return $this->redirectSummon($lead_id);
	}

	public function actionUpdate(int $summon_id, int $reminder_id) {
		$summonReminder = $this->findModel($summon_id, $reminder_id);

		$model = new SummonReminderForm();
		$model->setSummonReminder($summonReminder);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->asJson([
				'success' => true,
			]);
		}
		if ($model->hasErrors()) {
			return $this->asJson([
				'errors' => $model->getErrors(),
				'usersIds' => $model->usersRange,
			]);
		}
		return $this->renderAjax('form', [
			'model' => $model,
		]);
	}

	public function actionDelete(int $summon_id, int $reminder_id, string $returnUrl = null) {
		$model = $this->findModel($summon_id, $reminder_id);
		$model->delete();
		$model->reminder->delete();
		return $returnUrl ? $this->redirect($returnUrl) : $this->redirectSummon($summon_id);
	}

	public function actionDone(int $summon_id, int $reminder_id, string $returnUrl = null) {
		$model = $this->findModel($summon_id, $reminder_id);
		if (!$model->reminder->isDone()) {
			$model->reminder->markAsDone();
			$model->reminder->save();
		}
		if (Yii::$app->request->isAjax) {
			return $this->asJson(['success' => true]);
		}
		return $returnUrl ? $this->redirect($returnUrl) : $this->redirectSummon($summon_id);
	}

	public function actionNotDone(int $summon_id, int $reminder_id, string $returnUrl = null) {
		$model = $this->findModel($summon_id, $reminder_id);
		if ($model->reminder->isDone()) {
			$model->reminder->unmarkAsDone();
			$model->reminder->save();
		}
		return $returnUrl ? $this->redirect($returnUrl) : $this->redirectSummon($summon_id);
	}

	protected function redirectSummon(int $summon_id) {
		return $this->redirect([$this->summonView, 'id' => $summon_id]);
	}

	/**
	 * @throws NotFoundHttpException
	 * @throws ForbiddenHttpException
	 */
	private function findModel(int $summon_id, int $reminder_id): SummonReminder {
		$model = SummonReminder::find()
			->andWhere([
				'summon_id' => $summon_id,
				'reminder_id' => $reminder_id,
			])
			->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
		/** @var SummonReminder $model */
		if ($model->reminder->user_id !== null && Yii::$app->user->getId() !== $model->reminder->user_id) {
			throw new ForbiddenHttpException();
		}
		return $model;
	}

	private function findSummon(int $id): Summon {
		$summon = Summon::findOne($id);
		if ($summon === null) {
			throw new NotFoundHttpException();
		}
		if (!$summon->isForUser(Yii::$app->user->getId())) {
			if (!Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
				throw new ForbiddenHttpException('Only for Owner or Contractor');
			}
		}
		return $summon;
	}
}
