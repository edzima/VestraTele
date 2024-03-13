<?php

namespace console\controllers;

use common\models\KeyStorageItem;
use console\models\DemandForPayment;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;

class SettlementMessagesController extends Controller {

	public function actionFirstDemand(int $delayDays = null): void {
		$model = $this->createModel();
		$model->delayedDays = $delayDays;
		$model->which = DemandForPayment::WHICH_FIRST;
		$model->markMultiple();
	}

	public function actionSecondDemand(int $delayDays): void {
		$model = $this->createModel();
		$model->which = DemandForPayment::WHICH_SECOND;
		$model->delayedDays = $delayDays;
		$model->markMultiple();
	}

	public function actionThirdDemand(int $delayDays): void {
		$model = $this->createModel();
		$model->which = DemandForPayment::WHICH_THIRD;
		$model->delayedDays = $delayDays;
		$model->markMultiple();
	}

	protected function createModel(): DemandForPayment {
		$model = new DemandForPayment();
		$model->smsOwnerId = $this->getSmsOwnerId();
		$model->consoleController = $this;
		return $model;
	}

	private function getSmsOwnerId(): int {
		$owner = Yii::$app->keyStorage->get(KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID);
		if ($owner === null) {
			throw new InvalidConfigException('Not Set Robot SMS Owner. Key: (' . KeyStorageItem::KEY_ROBOT_SMS_OWNER_ID . ').');
		}
		return $owner;
	}
}
