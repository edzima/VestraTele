<?php

use console\base\Migration;

/**
 * Class m250115_201620_spi_user_auth
 */
class m250115_201620_spi_user_auth extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%spi_user_auth}}', [
			'id' => $this->primaryKey(),
			'user_id' => $this->integer()->notNull()->unique(),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
			'last_action_at' => $this->integer(),
			'username' => $this->string()->notNull(),
			'password' => $this->binary()->notNull(),
		]);

		$this->addForeignKey('{{%spi_user_auth_user}}',
			'{{%spi_user_auth}}', 'user_id',
			'{{%user}}', 'id',
			'CASCADE', 'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%spi_user_auth}}');
	}

}
