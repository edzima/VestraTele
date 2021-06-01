<?php

namespace console\controllers;

use common\models\issue\IssueMeet;
use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use console\components\MeetToLeadCreator;
use Yii;
use yii\base\BaseObject;
use yii\console\Controller;
use yii\db\IntegrityException;
use yii\helpers\Console;

class MeetController extends Controller {

	public function actionMigration(): void {
		IssueMeet::deleteAll(['id' => 9237]);
		Lead::deleteAll();
		foreach (IssueMeet::find()->andWhere(['>', 'id', 9236])->batch() as $rows) {
			foreach ($rows as $meet) {
				$creator = new MeetToLeadCreator();
				$lead = $creator->createLead($meet);
				if ($lead->validate(['status_id', 'source_id', 'campaign_id', 'phone', 'email'])) {
					$activeLead = Yii::$app->leadManager->pushLead($lead);
					if ($activeLead) {
						Console::output('Success push Lead from Meet: ' . $meet->id);
						try {
							$lead->linkUsers($activeLead);
						} catch (IntegrityException $exception) {
							Yii::error($exception->getMessage());
						}
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
