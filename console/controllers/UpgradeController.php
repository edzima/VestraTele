<?php

namespace console\controllers;

use common\models\issue\IssueMeet;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use yii\console\Controller;
use yii\helpers\Console;

class UpgradeController extends Controller {

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
