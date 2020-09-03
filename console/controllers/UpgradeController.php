<?php

namespace console\controllers;

use common\models\issue\IssueMeet;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class UpgradeController extends Controller {

	public function actionFixPayCalculation(): void {
		$ids = IssuePayCalculation::find()
			->select(['issue_id', 'id'])
			->asArray()
			->all();

		$map = ArrayHelper::map($ids, 'issue_id', 'id');
		foreach ($map as $issueId => $calculationId) {
			IssuePay::updateAll(['calculation_id' => $calculationId], ['issue_id' => $issueId]);
		}
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
