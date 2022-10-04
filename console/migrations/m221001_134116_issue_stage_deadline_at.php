<?php

use common\models\issue\Issue;
use common\models\issue\IssueStage;
use console\base\Migration;

/**
 * Class m221001_134116_issue_stage_deadline_at
 */
class m221001_134116_issue_stage_deadline_at extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_stage}}', 'calendar_background', $this->string()->null());
		$this->addColumn('{{%issue}}', '{{%stage_deadline_at}}', $this->dateTime()->null());
		foreach (Issue::find()
			->joinWith('stage')
			->andWhere(IssueStage::tableName() . '.days_reminder is NOT NULL')
			->batch() as $rows) {
			foreach ($rows as $row) {
				/**
				 * @var Issue $row
				 */
				$row->generateStageDeadlineAt();
				if ($row->stage_deadline_at) {
					$row->update([
						'stage_deadline_at',
					]);
				}
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_stage}}', 'calendar_background');
		$this->dropColumn('{{%issue}}', '{{%stage_deadline_at}}');
	}

}
