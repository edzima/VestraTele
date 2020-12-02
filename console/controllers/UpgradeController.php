<?php

namespace console\controllers;

use common\models\issue\IssueMeet;
use common\models\issue\IssuePayCalculation;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use yii\console\Controller;
use yii\helpers\Console;

class UpgradeController extends Controller {

	public function actionCalculationOwner(): void {
		IssuePayCalculation::updateAll(['owner_id' => 21]);
	}

	public function actionFixPhone(): void {
		$validator = new PhoneValidator();
		$validator->country = 'PL';
		foreach (IssueMeet::find()->batch() as $models) {
			foreach ($models as $model) {
				$validator->validateAttribute($model, 'phone');
				/** @var IssueMeet $model */
				if (!$model->hasErrors('phone')) {
					$model->update(false, ['phone']);
				} else {
					Console::output($model->id);
				}
			}
		}
	}

}
