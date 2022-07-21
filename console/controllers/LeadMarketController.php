<?php

namespace console\controllers;

use common\modules\lead\models\forms\LeadMarketCreateSummaryEmail;
use common\modules\lead\models\forms\LeadMarketReservedDeadlineEmail;
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

}
