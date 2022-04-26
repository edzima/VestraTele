<?php

use console\base\Migration;

/**
 * Class m220426_124316_newsmainpage
 */
class m220426_124316_news_mainpage extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%article}}', 'show_on_mainpage', $this->integer()->null());
		$this->createIndex('{{%article_mainpage_index}}', '{{%article}}', 'show_on_mainpage', false);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('{{%article_mainpage_index}}', '{{%article}}');

		$this->dropColumn('{{%article}}', 'show_on_mainpage');
	}

}
