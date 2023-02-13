<?php

namespace frontend\controllers;

use common\models\issue\search\SummonDocLinkSearch;
use common\models\issue\SummonDocLink;
use frontend\helpers\Url;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SummonDocController extends Controller {

	public function actionToDo(int $parentTypeId = null): string {
		$searchModel = new SummonDocLinkSearch();
		$searchModel->status = SummonDocLinkSearch::STATUS_TO_DO;
		$searchModel->issueParentTypeId = $parentTypeId;
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('to-do', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionToConfirm(int $parentTypeId = null): string {
		$searchModel = new SummonDocLinkSearch();
		$searchModel->status = SummonDocLinkSearch::STATUS_TO_CONFIRM;
		$searchModel->issueParentTypeId = $parentTypeId;
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('to-do', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionConfirmed(int $parentTypeId = null): string {
		$searchModel = new SummonDocLinkSearch();
		$searchModel->status = SummonDocLinkSearch::STATUS_CONFIRMED;
		$searchModel->issueParentTypeId = $parentTypeId;
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('to-do', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionDone(int $summon_id, int $doc_type_id, string $returnUrl = null) {
		$model = $this->findModel($summon_id, $doc_type_id);
		$model->done_user_id = Yii::$app->user->id;
		$model->done_at = date(DATE_ATOM);
		$model->save();
		if ($returnUrl === null) {
			$returnUrl = Url::previous();
		}
		return $this->redirect($returnUrl);
	}

	private function findModel(int $summon_id, int $doc_type_id): SummonDocLink {
		$model = SummonDocLink::find()
			->andWhere([
				'doc_type_id' => $doc_type_id,
				'summon_id' => $summon_id,
			])
			->one();
		if ($model === null) {
			throw new NotFoundHttpException();
		}
//		if (!$model->summon->isForUser(Yii::$app->user->getId())
//			|| !Yii::$app->user->can(Worker::PERMISSION_SUMMON_MANAGER)) {
//			throw new ForbiddenHttpException();
//		}
		return $model;
	}
}
