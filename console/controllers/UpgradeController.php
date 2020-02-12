<?php

namespace console\controllers;

use common\models\issue\Issue;
use common\models\issue\IssueMeet;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use Yii;
use yii\console\Controller;

class UpgradeController extends Controller {

	public function actionPaysVat(): void {
		IssuePay::updateAll(['vat' => 23]);
	}

	public function actionIssuePayed(): void {
		$ids = [];
		$models = Issue::find()
			->onlyPositiveDecision()
			->all();
		foreach ($models as $model) {
			$isPayed = true;
			foreach ($model->pays as $pay) {
				$isPayed = $isPayed && $pay->isPayed();
			}
			if ($isPayed) {
				$ids[] = $model->id;
			}
		}
		Yii::$app->db->createCommand()->update(IssuePayCalculation::tableName(), ['status' => IssuePayCalculation::STATUS_PAYED], ['issue_id' => $ids])
			->execute();
	}

	public function actionIssuePayDelayed(): void {
		$models = IssuePayCalculation::find()
			->where(['status' => IssuePayCalculation::STATUS_PAYED])
			->with('issue.pays')
			->all();
		$ids = [];

		foreach ($models as $model) {
			if (count($model->issue->pays) === 0) {
				$ids[] = $model->issue_id;
			}
		}
		Yii::$app->db->createCommand()->update(IssuePayCalculation::tableName(), ['status' => IssuePayCalculation::STATUS_DRAFT], ['issue_id' => $ids])
			->execute();
	}

	public function actionUpdateMeetStatus(): void {
		$statuses = [
			IssueMeet::STATUS_SIGNED_CONTRACT => [
				IssueMeet::STATUS_SENT_DOCUMENTS,
			],
			IssueMeet::STATUS_CONTACT_AGAIN => [
				IssueMeet::STATUS_WAITING_FOR_THE_RULE,
				IssueMeet::STATUS_WONDER,
			],
			IssueMeet::STATUS_NOT_ELIGIBLE => [
				IssueMeet::STATUS_EMERYT,
				IssueMeet::STATUS_RENTA,
				IssueMeet::STATUS_GUARDIAN_WORKS,
			],
		];
		$count = 0;
		foreach ($statuses as $status => $olds) {
			$count += IssueMeet::updateAll(['status' => $status], ['status' => $olds]);
		}
		$this->stdout("Update $count meets");
	}
}
