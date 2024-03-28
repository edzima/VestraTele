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
			'street' => $this->string()->notNull(),
			'postal_code' => $this->string(6)->notNull(),
			'type' => $this->string(2)->notNull(),
			'city_id' => $this->integer()->notNull(),
			'phone' => $this->string()->notNull(),
			'fax' => $this->string()->notNull(),
			'email' => $this->string()->notNull(),
		]);

		$this->addForeignKey('{{%fk_court_city}}', '{{%court}}', 'city_id', '{{%teryt_simc}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%court}}');
	}
}
