<?php

namespace console\controllers;

use common\modules\lead\models\forms\LeadMarketCreateSummaryEmail;
use common\modules\lead\models\forms\LeadMarketReservedDeadlineEmail;
use common\modules\lead\Module;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class LeadMarketController extends Controller {

	public function actionReservedDeadlineEmail(int $days): void {
		$model = new LeadMarketReservedDeadlineEmail();
		$model->days = $days;
		$count = $model->sendEmails();
		if ($count) {
			Console::output('Send Reserved Deadline Emails: ' . $count . '.');
		}
		if ($model->hasErrors()) {
			Console::output(VarDumper::export($model->getErrors()));
		}
	}

	public function actionCreateYesterdaySummaryEmail(): void {
		$model = new LeadMarketCreateSummaryEmail();
		$model->scenario = LeadMarketCreateSummaryEmail::SCENARIO_YESTERDAY;
		$count = $model->sendEmail();
		if ($count) {
			Console::output('Send Create Summary Email. Models: ' . $count . '.');
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
