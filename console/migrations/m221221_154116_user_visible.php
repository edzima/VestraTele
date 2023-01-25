<?php

use console\base\Migration;

/**
 * Class m221221_154116_user_visible
 */
class m221221_154116_user_visible extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%user_visible}}', [
			'user_id' => $this->integer()->notNull(),
			'to_user_id' => $this->integer()->notNull(),
			'status' => $this->integer()->notNull(),
		]);

		$this->addForeignKey('{{%fk_user_visible_user}}', '{{%user_visible}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_user_to_visible_user}}', '{{%user_visible}}', 'to_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addPrimaryKey('{{%PK_user_visible}}', '{{%user_visible}}', ['user_id', 'to_user_id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%user_visible}}');
	}

}
