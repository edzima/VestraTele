<?php

namespace console\controllers;

use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\IssueStage;
use common\models\issue\Summon;
use common\models\issue\SummonDocLink;
use DateTime;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class IssueUpgradeController extends Controller {

	public function actionLinkedIssuesNote(): void {
		$notes = IssueNote::find()
			->groupBy([
				'created_at',
				'description',
			])
			->having('COUNT(*) >1')
			->all();
		$notesIds = [];
		$deleteNotesCount = 0;
		$updateNotesCount = 0;
		foreach ($notes as $note) {
			$issue = $note->issue;
			$linkedIds = $issue->getLinkedIssuesIds();
			if (!empty($linkedIds)) {
				$deleteNotesCount += IssueNote::deleteAll(
					[
						'AND',
						['NOT', ['id' => $note->id]],  // array i.e [1,2]
						[
							'description' => $note->description,
							'issue_id' => $linkedIds,
						],

					]);

				$notesIds[] = $note->id;
			}
		}
		if (!empty($notesIds)) {
			$updateNotesCount = IssueNote::updateAll(['show_on_linked_issues' => ''], ['id' => $notesIds]);
		}
		Console::output('All Duplicated Notes: ' . count($notes));
		Console::output(var_dump(ArrayHelper::getColumn($notes, 'issue_id')));
		Console::output('Deleted Notes: ' . $deleteNotesCount);
		Console::output('Updated Notes: ' . $updateNotesCount);
	}

	public function actionSummonsRealizedAt(): void {
		Console::output(Summon::updateAll([
			'realized_at' => new Expression('FROM_UNIXTIME(updated_at)'),
		], [
			'status' => Summon::STATUS_REALIZED,
			'realized_at' => null,
		])
		);
	}

	public function actionSummonsRealizedDocs(): void {
		$summons = Summon::find()
			->select(['owner_id', 'realized_at', 'updated_at', 'id'])
			->andWhere(['status' => Summon::STATUS_REALIZED])
			->joinWith('docsLink')
			->andWhere(SummonDocLink::tableName() . '.confirmed_user_id IS NULL')
			->asArray()
			->all();
		$count = 0;
		foreach ($summons as $data) {
			$confirmed_at = !empty($data['realized_at'])
				? $data['realized_at']
				: date(DATE_ATOM, $data['updated_at']);
			$count += SummonDocLink::updateAll([
				'confirmed_user_id' => $data['owner_id'],
				'confirmed_at' => $confirmed_at,
			], [
				'summon_id' => $data['id'],
			]);
		}
		Console::output('Mark Docs as confirmed: ' . $count);
	}

	public function actionIssueStageDeadlineUpdate(): void {
		$stages = IssueStage::find()
			->andWhere('days_reminder IS NOT NULL')
			->indexBy('id')
			->all();

		if (empty($stages)) {
			Console::output('Not Found Stages with days reminder.');
			return;
		}
		foreach ($stages as $stage) {
			Console::output($stage->name . ' with ID: ' . $stage->id . ' days: ' . $stage->days_reminder);
		}
		Console::output('Find Stages with Days Reminders: ' . count($stages));
		$emptyStageChangeAt = Issue::updateAll(
			['stage_change_at' => 'created_at'],
			[
				'stage_change_at' => null,
				'stage_id' => array_keys($stages),
			]
		);
		Console::output('Update empty stage_change_at from created_at: ' . $emptyStageChangeAt);

		$daysStages = [];
		foreach ($stages as $stage) {
			$daysStages[$stage->days_reminder][] = $stage->id;
		}

		foreach ($daysStages as $days => $stagesIDs) {
			foreach ($stagesIDs as $stageId) {
				Console::output($stages[$stageId]->name);
			}
			$count = Issue::updateAll([
				'stage_deadline_at' => new Expression("DATE_ADD(stage_change_at, INTERVAL $days DAY)"),
			], [
				'stage_id' => $stagesIDs,
			]);
			Console::output("Updated Issues: $count  for Days: $days.\n");
		}
	}

	public function actionRestoreUpdateAt(string $date): void {
		$models = Issue::find()
			->select([Issue::tableName() . '.id', IssueNote::tableName() . '.publish_at'])
			->joinWith('newestNote')
			->andWhere(['>=', Issue::tableName() . '.updated_at', date('Y-m-d 00:00:00', strtotime($date))])
			->andWhere(['<=', Issue::tableName() . '.updated_at', date('Y-m-d 23:5:59', strtotime($date))])
			->andWhere(['>', IssueNote::tableName() . '.publish_at', Issue::tableName() . '.updated_at'])
			->distinct()
			->asArray()
			->all();
		$ids = ArrayHelper::map($models, 'id', 'newestNote.publish_at');
		Console::output(print_r($ids));
		Console::output(count($ids));

		foreach ($ids as $id => $updated_at) {
			Issue::updateAll(['updated_at' => $updated_at], [
				'id' => $id,
			]);
		}
		$models = Issue::find()
			->select([Issue::tableName() . '.id', Issue::tableName() . '.created_at'])
			->joinWith('newestNote')
			->andWhere(IssueNote::tableName() . '.id IS NULL')
			->andWhere(['>=', Issue::tableName() . '.updated_at', date('Y-m-d 00:00:00', strtotime($date))])
			->andWhere(['<=', Issue::tableName() . '.updated_at', date('Y-m-d 23:5:59', strtotime($date))])
			->asArray()
			->all();
		$ids = ArrayHelper::map($models, 'id', 'created_at');
		foreach ($ids as $id => $updated_at) {
			Issue::updateAll(['updated_at' => $updated_at], [
				'id' => $id,
			]);
		}
		Console::output(print_r($models));
		Console::output(count($models));
	}

	public function actionSummonTimes(): void {
		foreach (Summon::find()
			->batch() as $rows) {
			foreach ($rows as $model) {
				/** @var Summon $model */
				$realize = new DateTime($model->realize_at);
				$attributes = [];
				if ($realize->format('H') === '00') {
					$realize->setTime(date('H', $model->created_at), date('i', $model->created_at));
					$model->realize_at = $realize->format(DATE_ATOM);
					$attributes[] = 'realize_at';
				}
				$deadline = new DateTime($model->deadline_at);
				if ($deadline->format('H') === '00') {
					$deadline->setTime(date('H', $model->created_at), date('i', $model->created_at));
					$model->deadline_at = $deadline->format(DATE_ATOM);
					$attributes[] = 'deadline_at';
				}
				if (!empty($attributes)) {
					$model->updateAttributes($attributes);
				}
			}
		}
	}

}
