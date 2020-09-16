<?php

use console\base\Migration;

/**
 * Class m200916_102058_calendar_news
 */
class m200916_102058_calendar_news extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%calendar_news}}', [
			'id' => $this->primaryKey(),
			'user_id' => $this->integer()->notNull(),
			'text' => $this->text(),
			'start_at' => $this->dateTime(),
			'end_at' => $this->dateTime(),
		]);

		$this->addForeignKey('{{%fk_calendar_news_user}}', '{{%calendar_news}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%fk_calendar_news_user}}', '{{%calendar_news}}');
		$this->dropTable('{{%calendar_news}}');
	}

}
