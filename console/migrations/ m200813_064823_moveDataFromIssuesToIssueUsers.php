<?php

use yii\helpers\Console;
use console\base\Migration;
use \common\models\issue\Issue;
use \common\models\issue\IssueUser;

/**
 * Class m200813_064823_moveDataFromIssuesToIssueUsers
 */
class m200813_064823_moveDataFromIssuesToIssueUsers extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		foreach (Issue::find()->batch() as $models) {
			$rows = [];
			foreach ($models as $model) {
				$rows[] = [
					'user_id' => $model->agent_id,
					'issue_id' => $model->id,
					'type' => IssueUser::TYPE_AGENT,
				];
				if ($model->tele_id !== null) {
					$rows[] = [
						'user_id' => $model->tele_id,
						'issue_id' => $model->id,
						'type' => IssueUser::TYPE_TELEMARKETER,
					];
				}
				if ($model->lawyer_id !== null) {
					$rows[] = [
						'user_id' => $model->lawyer_id,
						'issue_id' => $model->id,
						'type' => IssueUser::TYPE_LAWYER,
					];
				}
			}
			$this->batchInsert('{{%issue_user}}', ['user_id', 'issue_id', 'type'], $rows);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		return false;
	}
}

