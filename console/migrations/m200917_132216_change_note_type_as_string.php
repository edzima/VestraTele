<?php

use console\base\Migration;

/**
 * Class m200917_132216_change_note_type_as_string
 */
class m200917_132216_change_note_type_as_string extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue_note}}', 'type', $this->string());
		$this->addColumn('{{%issue_note}}', 'publish_at', $this->integer());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('{{%issue_note}}', 'type', $this->smallInteger());
		$this->dropColumn('{{%issue_note}}', 'publish_at');
	}

}
