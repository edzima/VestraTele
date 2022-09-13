<?php

namespace console\controllers;

use backend\helpers\Url;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadUser;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class LeadFixController extends Controller {

	public function actionPhoneFormat(): void {
		Console::output('Leads Phone with " ": ' .
			Lead::find()
				->andWhere(['like', 'phone', ' '])
				->count()
		);
		Lead::updateAll([
			'phone' => new Expression("REPLACE(phone,' ','')"),
		], 'phone IS NOT NULL');

		Console::output('Leads Phone with "-": ' .
			Lead::find()
				->andWhere(['like', 'phone', '-'])
				->count()
		);

		Lead::updateAll([
			'phone' => new Expression("REPLACE(phone,'-','')"),
		], 'phone IS NOT NULL');
	}

	public function actionOwnerFromReports(): void {
		$query = Lead::find()
			->joinWith('leadUsers')
			->andWhere(['!=', 'lead_user.type', LeadUser::TYPE_OWNER])
			->andWhere(['NOT IN', 'source_id', [18, 19, 20, 21]]);

		foreach ($query->batch() as $rows) {
			foreach ($rows as $lead) {
				/** @var $lead Lead */
				if (!array_key_exists(LeadUser::TYPE_OWNER, $lead->getUsers())) {
					Console::output($lead->getId());
				}
			}
		}
	}

	public function actionEmptyContact(): void {
		$this->actionEmptyPhone();
		$this->actionEmptyEmail();
		Console::output('Empty Phone and Email: ') . Lead::deleteAll([
			'phone' => null,
			'email' => null,
		]);
	}

	public function actionEmptyPhone(): void {
		Console::output('Empty Phone as Null: ') . Lead::updateAll(['phone' => null], ['phone' => '']);
	}

	public function actionEmptyEmail(): void {
		Console::output('Empty Email as Null: ') . Lead::updateAll(['email' => null], ['email' => '']);
	}

	public function actionMergeStatus(int $status): void {

		$mergedIds = [];
		foreach (Lead::find()
			->andWhere(['=', 'status_id', $status])
			->andFilterWhere(['<>', 'id', $mergedIds])
			->batch() as $rows) {
			foreach ($rows as $lead) {

				/** @var Lead $lead */
				$leadId = $lead->id;
				if (isset($mergedIds[$leadId])) {
					Console::output("Lead: $leadId already merged.");
					continue;
				}
				$typeId = LeadSource::getModels()[$lead->source_id]->type_id;
				$sameContacts = $lead->getSameContacts();
				$sameContactsAndType = array_filter($sameContacts, static function (Lead $lead) use ($typeId) {
					return LeadSource::getModels()[$lead->source_id]->type_id === $typeId;
				});
				if (empty($sameContactsAndType)) {
					continue;
				}
				Console::output("\n");
				Console::output(Yii::getAlias('@backendUrl') . Url::to(['/lead/lead/view', 'id' => $leadId]));
				Console::output("Lead: $leadId has same contacts: " .
					implode(', ', ArrayHelper::getColumn($sameContactsAndType, 'id'))
				);

				if (count($sameContactsAndType) > 1) {
					Console::output(implode(', ', ArrayHelper::getColumn($sameContactsAndType, 'statusName')));
				}

				foreach ($sameContactsAndType as $sameLead) {
					/** @var Lead $sameLead */
					$sameId = $sameLead->id;
					$mergedIds[$sameId] = $sameId;
					if ($sameLead->getStatusId() !== $status) {
						Console::output(Yii::getAlias('@backendUrl') . Url::to(['/lead/lead/view', 'id' => $sameId]));
						if (Console::confirm("Same contacts: $sameId has Status: " . $sameLead->getStatusName() . '. Copy Report and Status from Parent?')) {
							$report = $this->findReport($lead, $status);
							if ($report) {
								$this->createAndSaveReport($sameLead, $report);
							}
							$sameLead->updateStatus($status);
						}
					}
				}
			}
		}
		Console::output('Merged Count: ' . count($mergedIds));
	}

	private function createAndSaveReport(Lead $lead, LeadReport $from): bool {
		$report = new LeadReport();
		$report->lead_id = $lead->getId();
		$report->status_id = $from->status_id;
		$report->old_status_id = $this->findOldStatus($lead, $from);
		$report->owner_id = $from->owner_id;
		$report->created_at = $from->created_at;
		$report->updated_at = $from->updated_at;
		$report->details = Yii::t('lead', 'Copy From: #' . $report->lead_id);
		if ($report->save(false)) {
			return true;
		}
		Console::output(print_r($report->getErrors()));

		return false;
	}

	private function findReport(Lead $lead, int $status): ?LeadReport {
		foreach ($lead->reports as $report) {
			if ($report->status_id === $status) {
				return $report;
			}
		}
		return null;
	}

	private function findOldStatus(Lead $lead, LeadReport $from): int {
		if (empty($lead->reports)) {
			return $lead->status_id;
		}
		$time = strtotime($from->created_at);
		foreach ($lead->reports as $report) {
			if (strtotime($report->created_at) < $time) {
				return $report->old_status_id;
			}
		}
		return $lead->status_id;
	}

}
