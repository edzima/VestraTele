<?php

use console\base\Migration;

/**
 * Class m240327_125715_court
 *
 * @see https://dane.gov.pl/pl/dataset/985,lista-sadow-powszechnych/resource/51607/table
 */
class m240327_125715_court extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%court}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'type' => $this->string(2)->notNull(),
			'phone' => $this->text()->notNull(),
			'fax' => $this->string()->null(),
			'email' => $this->string()->notNull(),
			'updated_at' => $this->date()->notNull(),
			'parent_id' => $this->integer()->null(),
		]);

		$this->addForeignKey('{{%fk_court_parent}}', '{{%court}}', 'parent_id', '{{%court}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%court_address}}', [
			'id' => $this->primaryKey(),
			'court_id' => $this->integer()->notNull(),
			'address_id' => $this->integer()->notNull(),
		]);

		$this->addForeignKey('{{%fk_court_address_court}}', '{{%court_address}}', 'court_id', '{{%court}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_court_address}}', '{{%court_address}}', 'address_id', '{{%address}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%court_address}}');
		$this->dropTable('{{%court}}');
	}
}
