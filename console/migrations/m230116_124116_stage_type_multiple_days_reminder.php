<?php

use console\base\Migration;

/**
 * Class m230116_124116_stage_type_multiple_days_reminder
 */
class m230116_124116_stage_type_multiple_days_reminder extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_stage_type}}', 'days_reminder_second', $this->integer()->null());
		$this->addColumn('{{%issue_stage_type}}', 'days_reminder_third', $this->integer()->null());
		$this->addColumn('{{%issue_stage_type}}', 'days_reminder_fourth', $this->integer()->null());
		$this->addColumn('{{%issue_stage_type}}', 'days_reminder_fifth', $this->integer()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_stage_type}}', 'days_reminder_second');
		$this->dropColumn('{{%issue_stage_type}}', 'days_reminder_third');
		$this->dropColumn('{{%issue_stage_type}}', 'days_reminder_fourth');
		$this->dropColumn('{{%issue_stage_type}}', 'days_reminder_fifth');
	}

}
