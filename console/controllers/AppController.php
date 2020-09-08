<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class AppController extends Controller {

	public function actionSetup() {
		Yii::$app->runAction('migrate/up', ['interactive' => $this->interactive]);
		Yii::$app->runAction('rbac/init', ['interactive' => $this->interactive]);
	}

}
