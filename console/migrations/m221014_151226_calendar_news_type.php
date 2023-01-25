<?php

use console\base\Migration;

/**
 * Class m221014_151226_calendar_news_type
 */
class m221014_151226_calendar_news_type extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%calendar_news}}', 'type', $this->string()->notNull());
		$this->createIndex('{{%calendar_news_type_index}}', '{{%calendar_news}}', 'type');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%calendar_news}}', 'type');
	}

}
