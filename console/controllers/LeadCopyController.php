<?php

namespace console\controllers;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use yii\console\Controller;
use yii\helpers\Console;

class LeadCopyController extends Controller {

	public function actionCopy(int $oldSourceId, int $newSourceId, int $status = LeadStatus::STATUS_NEW): void {
		if ($oldSourceId === $newSourceId) {
			Console::output('New source can not be equal as old');
			return;
		}
		$newSource = LeadSource::findOne($newSourceId);
		$oldSource = LeadSource::findOne($oldSourceId);
		if ($newSource === null || $oldSource === null) {
			Console::output('Sources not found');
			return;
		}
		$rows = [];
		$count = 0;
		$columns = [];
		foreach (Lead::find()
			->andWhere(['source_id' => $oldSource->id])
			->batch(1000) as $leads) {
			$rows = [];
			/**
			 * @var Lead $lead
			 */
			foreach ($leads as $lead) {
				$attributes = $lead->getAttributes();
				unset($attributes['id']);
				if (empty($count)) {
					$columns = array_keys($attributes);
				}
				$attributes['date_at'] = date(DATE_ATOM);
				$attributes['source_id'] = $newSource->id;
				$attributes['provider'] = Lead::PROVIDER_COPY;
				$attributes['updated_at'] = null;
				$attributes['status_id'] = $status;
				$rows[] = $attributes;
			}
			$count += Lead::getDb()->createCommand()
				->batchInsert(Lead::tableName(), $columns, $rows)
				->execute();
		}
		Console::output('Leads copied: ' . $count);
	}

}
