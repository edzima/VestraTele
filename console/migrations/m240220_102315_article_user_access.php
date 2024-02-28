<?php

use console\base\Migration;

/**
 * Class m240220_102315_article_user_access
 */
class m240220_102315_article_user_access extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%article_user}}', [
			'user_id' => $this->integer()->notNull(),
			'article_id' => $this->integer()->notNull(),
		]);
		$this->addPrimaryKey('{{%article_user_PK}}', '{{%article_user}}', ['user_id', 'article_id']);
		$this->addForeignKey('{{%article_user_article_FK}}', '{{%article_user}}', 'article_id', '{{%article}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%article_user_user_FK}}', '{{%article_user}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%article_user}}');
	}
}
