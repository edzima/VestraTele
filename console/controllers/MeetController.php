<?php

namespace console\controllers;

use common\models\issue\IssueMeet;
use common\modules\lead\models\Lead;
use common\modules\lead\Module;
use console\components\MeetToLeadCreator;
use yii\console\Controller;
use yii\helpers\Console;

class MeetController extends Controller {

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
