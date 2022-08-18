<?php

namespace console\controllers;

use common\modules\lead\models\forms\LeadMarketCreateSummaryEmail;
use common\modules\lead\models\forms\LeadMarketReservedDeadlineEmail;
use common\modules\lead\Module;
use yii\console\Controller;
use yii\helpers\Console;

class LeadMarketController extends Controller {

	public function actionReservedDeadlineEmail(int $days): void {
		$model = new LeadMarketReservedDeadlineEmail();
		$model->days = $days;
		$count = $model->sendEmails();
		Console::output('Send Reserved Deadline Emails: ' . $count . '.');
	}

	public function actionCreateYesterdaySummaryEmail(): void {
		$model = new LeadMarketCreateSummaryEmail();
		$model->scenario = LeadMarketCreateSummaryEmail::SCENARIO_YESTERDAY;
		$count = $model->sendEmail();
		Console::output('Send Create Summary Email. Models: ' . $count . '.');
	}

	public function actionTodaySummaryEmail(): void {
		$model = new LeadMarketCreateSummaryEmail();
		$model->scenario = LeadMarketCreateSummaryEmail::SCENARIO_TODAY;
		$model->emails = ['fachowosc@gmail.com'];
		$count = $model->sendEmail();
		if ($count) {
			Console::output('Send Create Summary Email. Models: ' . $count . '.');
		} else {
			Console::output('Not Found Model to Expired Renew.');
		}
	}

	public function actionExpiredRenew(): void {
		$count = Module::getInstance()->market->expiredRenew();
		if ($count) {
			Console::output('Expired Renew Models: ' . $count . '.');
		} else {
			Console::output('Not Found Model to Expired Renew.');
		}
	}

}
