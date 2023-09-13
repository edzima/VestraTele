<?php

use console\base\Migration;

/**
 * Class m220322_120416_issue_relation
 */
class m220322_120416_issue_relation extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%issue_relation}}', [
			'id' => $this->primaryKey(),
			'issue_id_1' => $this->integer()->notNull(),
			'issue_id_2' => $this->integer()->notNull(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
		]);

		$this->addForeignKey(
			'{{%issue_relation_issue_1}}',
			'{{%issue_relation}}',
			'issue_id_1',
			'{{%issue}}',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKey(
			'{{%issue_relation_issue_2}}',
			'{{%issue_relation}}',
			'issue_id_2',
			'{{%issue}}',
			'id',
			'CASCADE',
			'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%issue_relation}}');
	}

}
