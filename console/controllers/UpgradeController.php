<?php

namespace console\controllers;

use common\models\issue\Issue;
use common\models\issue\IssueMeet;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use udokmeci\yii2PhoneValidator\PhoneValidator;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class UpgradeController extends Controller {

	public function actionFixPhone(): void {
		$validator = new PhoneValidator();
		$validator->country = 'PL';
		foreach (IssueMeet::find()->batch() as $models) {
			foreach ($models as $model) {
				$validator->validateAttribute($model, 'phone');
				/** @var IssueMeet $model */
				if (!$model->hasErrors('phone')) {
					$model->update(false, ['phone']);
				} else {
					Console::output($model->id);
				}
			}
		}


	}

	public function actionRemoveUnusedTables(): void {

		$tables = [
			'{{%benefit_amount}}',
			'{{%cause}}',
			'{{%cause_category}}',
			'{{%score}}',
			'{{%task_status}}',
			'{{%task_uncertain}}',
			'{{%task}}',

		];
		$db = Yii::$app->db;
		foreach ($tables as $table) {
			$db->createCommand()->dropTable($table)->execute();
		}
	}

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

}
