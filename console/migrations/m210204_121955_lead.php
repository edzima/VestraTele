<?php

use console\base\Migration;

/**
 * Class m210204_121955_lead
 */
class m210204_121955_lead extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%lead}}', [
			'id' => $this->primaryKey(),
			'date_at' => $this->dateTime()->notNull(),
			'source' => $this->string()->notNull(),
			'data' => $this->json()->notNull(),
			'phone' => $this->string(30)->null(),
			'email' => $this->string(40)->null(),
			'postal_code' => $this->string(6)->null(),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%lead}}');
	}

}
