<?php

use console\base\Migration;

/**
 * Class m230808_140922_issue_file
 */
class m230808_140922_issue_file extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%issue_file}}', [
			'file_id' => $this->integer()->notNull(),
			'issue_id' => $this->integer()->notNull(),
			'details' => $this->string()->null(),
		]);

		$this->addPrimaryKey('{{%PK_issue_file}}', '{{%issue_file}}', [
			'file_id',
			'issue_id',
		]);

		$this->addForeignKey('{{%FK_issue_file_file}}', '{{%issue_file}}',
			'file_id', '{{%file}}', 'id',
			'CASCADE',
			'CASCADE');

		$this->addForeignKey('{{%FK_issue_file_issue}}', '{{%issue_file}}',
			'issue_id', '{{%issue}}', 'id',
			'CASCADE',
			'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%issue_file}}');
	}
}
