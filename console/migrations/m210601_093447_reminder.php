<?php

use console\base\Migration;

/**
 * Class m210601_093447_reminder
 */
class m210601_093447_reminder extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%reminder}}', [
			'id' => $this->primaryKey(),
			'priority' => $this->smallInteger()->notNull(),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
			'date_at' => $this->dateTime()->notNull(),
			'details' => $this->string(),
		]);

		$this->createTable('{{%lead_reminder}}', [
			'reminder_id' => $this->integer()->notNull(),
			'lead_id' => $this->integer()->notNull(),
		]);

		$this->addPrimaryKey('{{%PK_lead_reminder}}', '{{%lead_reminder}}', ['reminder_id', 'lead_id']);

		$this->addForeignKey('{{%FK_lead_reminder_reminder}}', '{{%lead_reminder}}', 'reminder_id', '{{%reminder}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%FK_lead_reminder_lead}}', '{{%lead_reminder}}', 'lead_id', '{{%lead}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%lead_reminder}}');
		$this->dropTable('{{%reminder}}');
	}

}
