<?php

namespace common\modules\lead\controllers;

use common\helpers\Flash;
use common\helpers\Html;
use common\modules\lead\models\Lead;
use common\modules\lead\models\searches\DuplicateLeadSearch;
use Yii;

class DuplicateController extends BaseController {

	public function actionIndex() {
		$searchModel = new DuplicateLeadSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		if (Yii::$app->request->isDelete) {
			$count = Lead::deleteAll(['id' => $searchModel->getAllIds($dataProvider->query)]);
			Flash::add(Flash::TYPE_SUCCESS,
				Yii::t('lead', 'Delete Leads: {count}.', [
					'count' => $count,
				])
			);
			return $this->refresh();
		}

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionDialers() {
		$searchModel = new DuplicateLeadSearch();
		return $this->redirect([
			'index',
			Html::getInputName($searchModel, 'onlyDialers') => true,
		]);
	}

}
