<?php

use console\base\Migration;

/**
 * Class m230530_132022_reminder_done_at
 */
class m230530_132022_reminder_done_at extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%reminder}}', 'done_at', $this->dateTime()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%reminder}}', 'done_at');
	}

}
