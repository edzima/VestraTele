<?php

namespace console\controllers;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use yii\console\Controller;
use yii\helpers\Console;

class LeadCopyController extends Controller {

	public function actionCopy(int $newSourceId, int $type = null, int $userID = null, int $oldSourceId = null, int $status = LeadStatus::STATUS_NEW): void {
		if ($oldSourceId === $newSourceId) {
			Console::output('New source can not be equal as old');
			return;
		}
		$newSource = LeadSource::findOne($newSourceId);
		if ($newSource === null) {
			Console::output('New source not found');
			return;
		}
		if ($oldSourceId) {
			$oldSource = LeadSource::findOne($oldSourceId);
			if ($oldSource === null) {
				Console::output('Old source not found');
				return;
			}
		}

		$count = 0;
		$columns = [];
		$query = Lead::find()
			->andFilterWhere(['source_id' => $oldSourceId]);
		if ($type) {
			$query->type($type);
		}
		if ($userID) {
			$query->user($userID);
		}
		$query->groupBy(Lead::tableName() . '.id');
		foreach ($query
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
