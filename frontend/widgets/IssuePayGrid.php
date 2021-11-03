<?php

namespace frontend\widgets;

use common\models\user\Worker;
use common\widgets\grid\IssuePayGrid as BaseIssuePayGrid;
use Yii;

class IssuePayGrid extends BaseIssuePayGrid {

	public ?string $payProvisionsRoute = '/pay/pay-provisions';
	public ?string $payRoute = '/pay/pay';
	public ?string $updateRoute = '/pay/update';
	public ?string $receivedRoute = '/pay-received/received';
	public ?string $deleteRoute = null;
	public ?string $statusRoute = null;

	public function init(): void {
		if (!Yii::$app->user->can(Worker::PERMISSION_PAY_RECEIVED)) {
			$this->receivedRoute = null;
		}
		if (!Yii::$app->user->can(Worker::PERMISSION_PAY_UPDATE)) {
			$this->updateRoute = null;
		}
		if (!Yii::$app->user->can(Worker::PERMISSION_PAY_PAID)) {
			$this->payRoute = null;
		}
		parent::init();
	}

}
