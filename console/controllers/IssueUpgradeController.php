<?php

namespace console\controllers;

use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\IssueUser;
use common\models\issue\Summon;
use DateTime;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class IssueUpgradeController extends Controller {

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

	public function actionCheckLead(): void {
		$ids = [];
		foreach (IssueUser::find()
			->withType(IssueUser::TYPE_CUSTOMER)
			->with('user.userProfile')
			->batch() as $rows) {
			foreach ($rows as $issueUser) {
				/** @var $issueUser IssueUser */
				$userProfile = $issueUser->user->profile;
				$phones = [];
				if (!empty($userProfile->phone)) {
					$phones[] = $userProfile->phone;
				}
				if (!empty($userProfile->phone_2)) {
					$phones[] = $userProfile->phone_2;
				}
				$meetIds = IssueMeet::find()->
				select('id')
					->andWhere(['phone' => $phones])
					->column();

				foreach ($meetIds as $id) {
					$ids[$id] = $id;
				}
			}
		}
		Console::output(print_r($ids));
		Console::output('Count: ' . count($ids));
	}
}
