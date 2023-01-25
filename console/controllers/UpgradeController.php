<?php

namespace console\controllers;

use backend\modules\settlement\models\CalculationProblemStatusForm;
use common\components\DbManager;
use common\helpers\StringHelper;
use common\models\CalendarNews;
use common\models\issue\IssueNote;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\models\provision\IssueProvisionType;
use common\models\user\Customer;
use common\models\user\UserProfile;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use DateTime;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;

class UpgradeController extends Controller {

	public function actionCalendarNewsType(): void {
		CalendarNews::updateAll(['type' => CalendarNews::TYPE_SUMMON]);
	}

	public function actionIssueProvisions(): void {
		$a = '10.3';

		Console::output(is_numeric($a));
	}

	public function actionLeadSmsStatus(int $status_id, string $details): void {
		$models = LeadReport::find()
			->select(['id', 'lead_id'])
			->andWhere(['like', 'details', $details])
			->asArray()
			->all();

		LeadReport::updateAll([
			'status_id' => $status_id,
		],
			[
				'id' => ArrayHelper::getColumn($models, 'id'),
			]
		);
		Lead::updateAll([
				'status_id' => $status_id,
			]
			, [
				'id' => ArrayHelper::getColumn($models, 'lead_id'),
			]
		);
	}

	public function actionReports(int $owner_id): void {
		$reports = LeadReport::find()
			->joinWith('lead')
			->andWhere([LeadReport::tableName() . '.owner_id' => $owner_id])
			->all();
		foreach ($reports as $report) {
			$lead = $report->lead;
			$lead->status_id = $report->old_status_id;
			$lead->save();
			$report->delete();
		}
	}

	public function actionLeads(int $newStatus, int $owner_id): void {
		foreach (Lead::find()
			->distinct()
			->andWhere(['!=', 'status_id', $newStatus])
			->andWhere('lead.phone IS NOT NULL')
			->leftJoin(UserProfile::tableName() . ' UP', 'UP.phone = lead.phone OR UP.phone_2 = lead.phone')
			->andWhere('UP.user_id IS NOT NULL')
			->batch() as $rows) {
			foreach ($rows as $lead) {
				/** @var Lead $lead */
				$report = new ReportForm();
				$report->setLead($lead);
				$report->withAddress = false;
				$report->withAnswers = false;
				$report->owner_id = $owner_id;
				$report->status_id = $newStatus;
				$report->save(false);
			}
		}
	}

	public function actionLeadEmptyPhone(): void {
		Lead::updateAll(['phone' => null], ['phone' => '+48 33 322 21 11']);
		Lead::updateAll(['phone' => null], ['phone' => '']);
	}

	public function actionNoteTitleDates(): void {
		$count = 0;
		foreach (IssueNote::find()->andWhere(['like', 'title', '('])->batch(500) as $rows) {
			foreach ($rows as $note) {
				/** @var IssueNote $note */
				$date = StringHelper::between($note->title, '(', ')');
				$dateTime = null;
				if ($date) {
					try {
						$dateTime = new DateTime($date);
						$count++;
						$note->detachBehaviors();
						$note->updateIssueAfterSave = false;
						$note->publish_at = $dateTime->format('Y-m-d');
						$note->title = trim(str_replace("($date)", '', $note->title));
						if (!$note->save()) {
							Console::output(print_r($note->getErrors()));
						}
					} catch (Exception $exception) {
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

}
