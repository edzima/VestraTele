<?php

namespace frontend\controllers;

use common\models\issue\search\SummonDocLinkSearch;
use Yii;
use yii\web\Controller;

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
}
