<?php

namespace console\controllers;

use common\modules\court\Module;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\helpers\Console;

class LawsuitController extends Controller {

	public function actionSpiSync(int $authUserId, bool $sessions, string $interval = '24 hour') {
		$this->stdout("SPI SYNC for User: $authUserId\n");

		/**
		 * @var Module $courtModule
		 */
		$courtModule = Yii::$app->getModule('court');
		if ($courtModule === null) {
			throw new InvalidConfigException('court module must be set.');
		}

		$syncSpi = $courtModule->getSpiSync($authUserId);
		if ($syncSpi === null) {
			throw new InvalidConfigException('Sync SPI component must be set.');
		}
		if (!$sessions) {
			$syncSpi->sessionSync = false;
		}
		$count = $syncSpi->all($interval);
		Console::output('Sync Lawsuits with SPI: ' . $count);
	}
}
