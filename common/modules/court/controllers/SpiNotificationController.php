<?php

namespace common\modules\court\controllers;

use common\modules\court\modules\spi\controllers\NotificationController;

class SpiNotificationController extends NotificationController {

	public function actionView(int $id, string $appeal, string $signature = null, string $court = null) {
		return $this->redirect([
			'/court/lawsuit/spi-lawsuit',
			'notificationId' => $id,
			'appeal' => $appeal,
			'signature' => $signature,
			'court' => $court,
		]);
	}

	public function actionRead(string $appeal, int $id, string $signature = null, string $court = null, string $returnUrl = null) {
		$this->repository->setAppeal($appeal);
		$this->repository->read($id);
		if ($returnUrl) {
			return $this->redirect($returnUrl);
		}

		return $this->redirect([
			'/court/lawsuit/spi-lawsuit',
			'notificationId' => $id,
			'appeal' => $appeal,
			'signature' => $signature,
			'court' => $court,
		]);
	}
}
