<?php

namespace console\controllers;

use backend\modules\settlement\models\CalculationProblemStatusForm;
use common\components\DbManager;
use common\models\issue\IssueMeet;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueStage;
use common\models\user\Customer;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class UpgradeController extends Controller {

	public function actionCustomerSummon(): void {
		/** @var DbManager $auth */
		$auth = Yii::$app->authManager;
		$auth->db->createCommand()
			->delete($auth->assignmentTable, [
				'user_id' => Customer::getAssignmentIds([Customer::PERMISSION_SUMMON], false),
				'item_name' => Customer::PERMISSION_SUMMON,
			])
			->execute();
		Console::output(Customer::find()->onlyAssignments([Customer::PERMISSION_SUMMON], false)->count());
	}

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
