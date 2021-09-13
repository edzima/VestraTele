<?php

namespace console\controllers;

use backend\modules\settlement\models\CalculationProblemStatusForm;
use common\components\DbManager;
use common\helpers\StringHelper;
use common\models\issue\IssueMeet;
use common\models\issue\IssueNote;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\models\provision\IssueProvisionType;
use common\models\user\Customer;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Json;

class UpgradeController extends Controller {

	public function actionNoteTitleDates(): void {
		$count = 0;
		foreach (IssueNote::find()->andWhere(['like', 'title', '('])->batch(500) as $rows) {
			foreach ($rows as $note) {
				/** @var IssueNote $note */
				$date = StringHelper::between($note->title, '(', ')');
				$dateTime = null;
				if ($date) {
					try {
						$dateTime = new \DateTime($date);
						$count++;
						$note->detachBehaviors();
						$note->publish_at = $dateTime->format('Y-m-d');
						$note->title = trim(str_replace("($date)", '', $note->title));
						if (!$note->save()) {
							Console::output(print_r($note->getErrors()));
						}
					} catch (\Exception $exception) {
						Console::output($date);
					}
				}
			}
		}
		Console::output('Total Count: ' . IssueNote::find()->andWhere(['like', 'title', '('])->count());
		Console::output('With Valid Date Count: ' . $count);
	}

	public function actionProvisionTypeUsers(): void {
		/** @var IssueProvisionType[] $types */
		$types = IssueProvisionType::find()->all();
		foreach ($types as $type) {
			$data = Json::decode($type->data) ?? [];
			$roles = $data['roles'] ?? [];
			$role = reset($roles);
			unset($data['roles']);
			$type->data = Json::encode($data);
			if (!$role) {
				$role = IssueUser::TYPE_AGENT;
			}
			$type->setIssueUserTypes($role);
			$type->save();
		}
	}

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
			->onlyUnpaid()
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
