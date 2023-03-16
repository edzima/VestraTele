<?php

use console\base\Migration;

/**
 * Class m230313_103522_issue_note_show_in_linked_issues
 */
class m230313_103522_issue_note_show_in_linked_issues extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_note}}', 'show_on_linked_issues', $this->string()->null());
		$this->addColumn('{{%issue_type}}', 'default_show_linked_notes', $this->boolean()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_note}}', 'show_on_linked_issues');
		$this->dropColumn('{{%issue_type}}', 'default_show_linked_notes');
	}

}
