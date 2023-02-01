<?php

use console\base\Migration;

/**
 * Class m221228_124116_lead_status_calendar_background
 */
class m230131_124116_issue_note_updater_id extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_note}}', 'updater_id', $this->integer()->null());
		$this->addForeignKey('{{%fk_issue_note_updater}}', '{{%issue_note}}', 'updater_id', '{{%user}}', 'id');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%fk_issue_note_updater}}', '{{%issue_note}}');
		$this->dropColumn('{{%issue_note}}', 'updater_id');
	}

}
