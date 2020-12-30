<?php

namespace frontend\widgets;

use common\models\settlement\PayInterface;
use common\models\user\User;
use common\widgets\grid\IssuePayGrid as BaseIssuePayGrid;
use Yii;

class IssuePayGrid extends BaseIssuePayGrid {

	public ?string $payProvisionsRoute = '/pay/pay-provisions';
	public ?string $updateRoute = '/pay/update';
	public ?string $receivedRoute = '/pay-received/received';

	public function init(): void {
		if (!Yii::$app->user->can(User::PERMISSION_PAY_RECEIVED)) {
			$this->receivedRoute = null;
		}
		parent::init();
	}

	protected function actionColumn(): array {
		$column = parent::actionColumn();
		$column['visibleButtons'] = [
			'pay' => false,
			'received' => function (PayInterface $pay): bool {
				return !$pay->isPayed();
			},
			'update' => function (PayInterface $pay): bool {
				return !$pay->isPayed() && $this->updateRoute !== null;
			},
			'status' => false,
			'delete' => false,
		];
		return $column;
	}
}
