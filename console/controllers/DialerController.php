<?php

namespace console\controllers;

use common\modules\lead\models\LeadDialerType;
use yii\console\Controller;
use yii\helpers\Console;

class DialerController extends Controller {

	public function actionActivateType(int $id): void {
		$model = LeadDialerType::findOne($id);
		if (!$model) {
			Console::output('Not Found Type with ID: ' . $id);
		} elseif ($model->status !== LeadDialerType::STATUS_ACTIVE) {
			$model->status = LeadDialerType::STATUS_ACTIVE;
			if ($model->save()) {
				Console::output('Success Activate Type: ' . $model->name);
			}
		} else {
			Console::output('Type: ' . $model->name . ' is already active.');
		}
	}

	public function actionInactivateType(int $id): void {
		$model = LeadDialerType::findOne($id);
		if (!$model) {
			Console::output('Not Found Type with ID: ' . $id);
		} elseif ($model->status !== LeadDialerType::STATUS_INACTIVE) {
			$model->status = LeadDialerType::STATUS_INACTIVE;
			if ($model->save()) {
				Console::output('Success Inactive Type: ' . $model->name);
			}
		} else {
			Console::output('Type: ' . $model->name . ' is already inactive.');
		}
	}

}
