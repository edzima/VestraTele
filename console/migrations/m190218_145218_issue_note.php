<?php

use console\base\Migration;

class m190218_145218_issue_note extends Migration {

	public function safeUp(): void {

		$this->createTable('{{%issue_note}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer()->notNull(),
			'user_id' => $this->integer()->notNull(),
			'type' => $this->smallInteger(),
			'title' => $this->string()->notNull(),
			'description' => $this->text()->notNull(),
			'created_at' => $this->timestamp(),
			'updated_at' => $this->timestamp(),
		]);
		$this->addForeignKey('fk_issue_note_user', '{{%issue_note}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_note_issue', '{{%issue_note}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');
	}

	public function safeDown(): void {
		$this->dropTable('{{%issue_note}}');
	}

}
