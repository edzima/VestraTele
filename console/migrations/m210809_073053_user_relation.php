<?php

use console\base\Migration;

/**
 * Class m210809_073053_user_hierarchy_excluded
 */
class m210809_073053_user_relation extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%user_relation}}', [
			'user_id' => $this->integer()->notNull(),
			'to_user_id' => $this->integer()->notNull(),
			'type' => $this->string()->notNull(),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
		]);

		$this->addPrimaryKey('{{%pk_user_relation}}', '{{%user_relation}}', ['user_id', 'to_user_id', 'type']);

		$this->createIndex('{{%index_user_relation}}', '{{%user_relation}}', 'type', false);
		$this->addForeignKey('{{%fk_user_relation_user}}', '{{%user_relation}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_user_relation_to_user}}', '{{%user_relation}}', 'to_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('{{%index_user_relation}}', '{{%user_relation}}');
		$this->dropTable('{{%user_relation}}');
	}

}
