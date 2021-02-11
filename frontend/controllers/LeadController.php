<?php

namespace frontend\controllers;

use common\modules\lead\models\LeadEntity;
use Yii;
use yii\rest\Controller;

class LeadController extends Controller {

	public function actionLanding() {
		Yii::$app->leadManager->pushLead(new LeadEntity(Yii::$app->request->post()));
	}
}
