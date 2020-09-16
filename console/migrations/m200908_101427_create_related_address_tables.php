<?php

use console\base\Migration;

/**
 * Class m200908_101427_create_related_address_tables
 */
class m200908_101427_create_related_address_tables extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%address}}', [
			'id' => $this->primaryKey(),
			'city_id' => $this->integer(),
			'postal_code' => $this->string(6),
			'info' => $this->string(100),
		]);

		$this->addForeignKey('{{%fk_address_city}}', '{{%address}}', 'city_id', '{{%teryt_simc}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%user_address}}', [
			'user_id' => $this->integer()->notNull(),
			'address_id' => $this->integer()->notNull(),
			'type' => $this->smallInteger()->notNull(),
		]);

		$this->addPrimaryKey('{{%pk_user_address}}', '{{%user_address}}', ['user_id', 'type']);

		$this->addForeignKey('{{%fk_user_address_user}}', '{{%user_address}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%meet_address}}', [
			'meet_id' => $this->integer()->notNull(),
			'address_id' => $this->integer()->notNull(),
			'type' => $this->smallInteger()->notNull(),
		]);
		$this->addPrimaryKey('{{%pk_meet_address}}', '{{%meet_address}}', ['meet_id', 'type']);

		$this->addForeignKey('{{%fk_meet_address_meet}}', '{{%meet_address}}', 'meet_id', '{{%issue_meet}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%meet_address}}');
		$this->dropTable('{{%user_address}}');
		$this->dropTable('{{%address}}');
	}

}
