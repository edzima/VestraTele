<?php

namespace backend\modules\user\controllers;

use backend\modules\user\models\CustomerUserForm;
use backend\modules\user\models\event\UserFormEvent;
use backend\modules\user\models\search\CustomerUserSearch;
use backend\modules\user\models\UserForm;
use common\models\PotentialClient;
use common\models\user\Customer;
use Yii;
use yii\base\Event;
use yii\data\ActiveDataProvider;

class CustomerController extends UserController {

	public $searchModel = CustomerUserSearch::class;
	public $formModel = CustomerUserForm::class;
	public $model = Customer::class;

	public function actionCreate() {
		Event::on(CustomerUserForm::class, CustomerUserForm::EVENT_AFTER_SAVE, function (UserFormEvent $event): void {
			$this->createPotentialClientAgreement($event->sender);
		});
		return parent::actionCreate();
	}

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
			'model' => $model,
			'issuesDataProvider' => $dataProvider,
		]);
	}


	protected function createPotentialClientAgreement(UserForm $form): void {
		if (!empty($form->getProfile()->birthday)) {
			$model = new PotentialClient();
			$model->birthday = $form->getProfile()->birthday;
			$model->firstname = $form->getProfile()->firstname;
			$model->lastname = $form->getProfile()->lastname;
			$model->status = PotentialClient::STATUS_AGREEMENT;
			$model->city_id = $form->getHomeAddress()->city_id;
			$model->owner_id = Yii::$app->user->getId();
			$model->save();
		}
	}

}
