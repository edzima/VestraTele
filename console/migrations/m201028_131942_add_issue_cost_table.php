<?php

use console\base\Migration;

/**
 * Class m201028_131942_add_issue_cost_table
 */
class m201028_131942_add_issue_cost_table extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp(): void {

		$this->createTable('{{%issue_cost}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer()->notNull(),
			'type' => $this->string(30)->notNull(),
			'value' => $this->decimal(10, 2),
			'vat' => $this->decimal(5, 2),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
			'date_at' => $this->date()->notNull(),
		]);
		$this->createIndex('index_issue_cost_type', '{{%issue_cost}}', 'type');
		$this->addForeignKey('fk_issue_cost_issue', '{{%issue_cost}}', 'issue_id', '{{%issue}}', 'id');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): void {
		$this->dropTable('{{%issue_cost}}');
	}

}
