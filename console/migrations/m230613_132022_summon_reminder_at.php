<?php

use console\base\Migration;

/**
 * Class m230613_132022_summon_reminder_at
 */
class m230613_132022_summon_reminder_at extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {


		$this->createTable('{{%summon_reminder}}', [
			'summon_id' => $this->integer()->notNull(),
			'reminder_id' => $this->integer()->notNull(),
		]);

		$this->addForeignKey('{{%FK_summon_reminder_reminder}}', '{{%summon_reminder}}', 'reminder_id', '{{%reminder}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%FK_summon_reminder_summon}}', '{{%summon_reminder}}', 'summon_id', '{{%summon}}', 'id', 'CASCADE', 'CASCADE');
		$this->addPrimaryKey('{{%PK_summon_reminder}}', '{{%summon_reminder}}', ['reminder_id', 'summon_id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%summon_reminder}}');
	}
}
