<?php

namespace console\controllers;

use common\models\Address;
use common\models\issue\IssueMeet;
use common\models\issue\MeetAddress;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadAddress;
use common\modules\lead\models\searches\LeadSearch;
use common\modules\lead\Module;
use console\components\MeetToLeadCreator;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * @deprecated
 */
class MeetController extends Controller {

	public function actionEmptyPhone(): void {
		IssueMeet::updateAll(['phone' => null], ['phone' => '+48 33 322 21 11']);
		IssueMeet::updateAll(['phone' => null], ['phone' => '']);
	}

	public function actionLeadAddress(): void {
		foreach (IssueMeet::find()
			->joinWith('addresses')
			->andWhere(['!=', 'phone', ''])
			->batch() as $rows) {
			foreach ($rows as $row) {
				$data = [];
				/** @var $row IssueMeet */
				if (isset($row->addresses[MeetAddress::TYPE_CUSTOMER]) || $row->city_id) {
					$meetAddress = $row->addresses[MeetAddress::TYPE_CUSTOMER] ?? null;
					if (!$meetAddress) {
						$address = new Address();
						$address->city_id = $row->city_id;
						$address->info = $row->street;
						$address->save();
						$addressId = $address->id;
					} else {
						$addressId = $meetAddress->address_id;
					}
					$search = new LeadSearch();
					$search->phone = $row->phone;
					/** @var Lead[] $leads */
					$leads = $search->search()->getModels();
					if (!empty($leads)) {
						Console::output('Find Leads: ' . count($leads) . ' with phone: ' . $row->phone);
					}
					foreach ($leads as $lead) {
						if ($lead->getCustomerAddress() === null) {
							$data[] = [
								'lead_id' => $lead->id,
								'address_id' => $addressId,
								'type' => LeadAddress::TYPE_CUSTOMER,
							];
						}
					}
					if (!empty($data)) {
						LeadAddress::getDb()->createCommand()
							->batchInsert(LeadAddress::tableName(), [
								'lead_id',
								'address_id',
								'type',
							],
								$data)->execute();
					}
				}
			}
		}
	}

	public function actionMigration(): void {
		Lead::deleteAll();

		$manager = Module::manager();
		foreach (IssueMeet::find()->batch() as $rows) {
			foreach ($rows as $meet) {
				$creator = new MeetToLeadCreator();
				$lead = $creator->createLead($meet);
				if ($lead->validate(['status_id', 'source_id', 'campaign_id', 'phone', 'email'])) {
					$activeLead = $manager->pushLead($lead);
					if ($activeLead) {
						Console::output('Success push Lead from Meet: ' . $meet->id);
						$report = $creator->createReport($activeLead, $meet);
						if ($report && $report->validate() && $report->save()) {
							Console::output('Success save Report.');
						} else {
							if ($report && $report->hasErrors()) {
								Console::output(print_r($report->getErrors()));
							}
						}
					}
				} else {
					Console::output(print_r($lead->getErrors()));
				}
			}
		}
	}

}
