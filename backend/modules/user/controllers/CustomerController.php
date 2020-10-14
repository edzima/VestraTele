<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\CustomerUserForm;
use backend\modules\user\models\search\CustomerUserSearch;
use common\models\user\Customer;
use yii\data\ActiveDataProvider;

class CustomerController extends UserController {

	public $searchModel = CustomerUserSearch::class;
	public $formModel = CustomerUserForm::class;
	public $model = Customer::class;

	public function actionView(int $id): string {
		$model = $this->findModel($id);
		$query = $model->getIssueUsers();
		$query->with([
			'issue',
			'issue.type',
			'issue.stage',
		]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		return $this->render('view', [
			'model' => $this->findModel($id),
			'issuesDataProvider' => $dataProvider,
		]);
	}
}
