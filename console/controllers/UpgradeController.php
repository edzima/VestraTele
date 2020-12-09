<?php

namespace console\controllers;

use backend\modules\settlement\models\CalculationProblemStatusForm;
use common\models\issue\IssueMeet;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueStage;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use yii\console\Controller;
use yii\helpers\Console;

class UpgradeController extends Controller {

	public function actionCalculationOwner(): void {
		IssuePayCalculation::updateAll(['owner_id' => 21]);
	}

	public function actionPayType(): void {
		IssuePayCalculation::updateAll(['type' => IssuePayCalculation::TYPE_HONORARIUM]);
	}

	public function actionProblemsPays(): void {
		IssuePay::updateAll(['status' => null], ['or', 'status=0', 'pay_at IS NOT NULL']);
		$pays = IssuePay::find()
			->onlyNotPayed()
			->andWhere('status IS NOT NULL')
			->indexBy('calculation_id')
			->with('calculation')
			->all();
		foreach ($pays as $pay) {
			$model = new CalculationProblemStatusForm($pay->calculation);
			$model->status = IssuePayCalculation::PROBLEM_STATUS_PREPEND_DEMAND;
			$model->save();
		}
		Console::output(count($pays));
	}

	public function actionCalculationStage(): void {
		IssuePayCalculation::updateAll(['stage_id' => IssueStage::POSITIVE_DECISION_ID]);
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
