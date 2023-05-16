<?php

namespace backend\controllers;

use yii\web\Controller;

class CustomerController extends Controller {

	public function actionView(int $id) {
		return $this->redirect(['/user/customer/view', 'id' => $id]);
	}
}
