<?php

use console\base\Migration;

/**
 * Class m200810_165729_issue_user
 */
class m200929_095705_issue_user_table extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%issue_user}}', [
			'user_id' => $this->integer()->notNull(),
			'issue_id' => $this->integer()->notNull(),
			'type' => $this->string()->notNull(),
		]);

		$this->addPrimaryKey('{{%pk_issue_user}}', '{{%issue_user}}', ['user_id', 'issue_id', 'type']);

		$this->addForeignKey('{{%fk_issue_user_user}}', '{{%issue_user}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_issue_user_issue}}', '{{%issue_user}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');
		$this->createIndex('{{%issue_user_type_index}}', '{{%issue_user}}', 'type');

		$this->dropForeignKey('fk_issue_client_agent', '{{%issue}}');
		$this->dropForeignKey('fk_issue_lawyer', '{{%issue}}');
		$this->dropForeignKey('fk_issue_tele', '{{%issue}}');


	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addForeignKey('fk_issue_client_agent', '{{%issue}}', 'agent_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_lawyer', '{{%issue}}', 'lawyer_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_tele', '{{%issue}}', 'tele_id', '{{%user}}', 'id');

		$this->dropTable('{{%issue_user}}');
	}

}
