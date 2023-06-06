<?php

use console\base\Migration;

/**
 * Class m230523_132022_reminder_user_id
 */
class m230523_132022_reminder_user_id extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%reminder}}', 'user_id', $this->integer()->null());
		$this->addForeignKey('{{%reminder_user_FK}}', '{{%reminder}}', 'user_id', '{{%user}}', 'id', 'SET NULL', 'SET NULL');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%reminder_user_FK}}', '{{%reminder}}');
		$this->dropColumn('{{%reminder}}', 'user_id');
	}

}
