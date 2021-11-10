<?php

namespace console\controllers;

use console\models\DemandForPayment;

class SettlementMessagesController {

	public function actionFirstDemand(int $delayDays): void {
		$model = new DemandForPayment();
		$model->delayedDays = $delayDays;
		$model->which = DemandForPayment::WHICH_FIRST;
		$model->markAll();
	}

	public function actionSecondDemand(int $delayDays): void {
		$model = new DemandForPayment();
		$model->which = DemandForPayment::WHICH_SECOND;
		$model->delayedDays = $delayDays;
		$model->markAll();
	}

	public function actionThirdDemand(int $delayDays): void {
		$model = new DemandForPayment();
		$model->which = DemandForPayment::WHICH_SECOND;
		$model->delayedDays = $delayDays;
		$model->markAll();
	}
}
