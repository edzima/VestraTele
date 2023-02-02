<?php

namespace console\controllers;

use backend\modules\issue\models\IssueStage;
use common\models\issue\Issue;
use common\models\KeyStorageItem;
use Yii;
use yii\base\InvalidArgumentException;
use yii\console\Controller;
use yii\helpers\Console;

class IssueArchiveController extends Controller {

	public function actionDeep(int $days = null): void {
		if ($days === null) {
			$days = Yii::$app->keyStorage->get(KeyStorageItem::KEY_ISSUE_DEEP_ARCHIVE_DAYS);
		}
		if ($days <= 0) {
			throw new InvalidArgumentException('Days must be greater than 0.');
		}
		$date = new \DateTime();
		$date->modify("- $days days");
		$date->format('Y-m-d');
		Console::output($date->format('Y-m-d'));
		$count = Issue::updateAll([
			'stage_id' => IssueStage::ARCHIVES_DEEP_ID,
		], 'stage_id = :stage_id AND stage_change_at < :date', [
			'stage_id' => IssueStage::ARCHIVES_ID,
			'date' => $date->format('Y-m-d'),
		]);
		Console::output('Move Issues to Deep Archive: ' . $count . '.');
	}
}
