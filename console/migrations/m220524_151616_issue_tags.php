<?php

use console\base\Migration;

/**
 * Class m220524_151616_issue_tags
 *
 * @see IssueTag
 * @see IssueTagLink
 */
class m220524_151616_issue_tags extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%issue_tag}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'description' => $this->string(),
			'type' => $this->string()->null(),
			'is_active' => $this->boolean(),
		]);

		$this->createTable('{{%issue_tag_link}}', [
			'tag_id' => $this->integer()->notNull(),
			'issue_id' => $this->integer()->notNull(),
		]);

		$this->addForeignKey('{{%fk_issue_tag_issue}}',
			'{{%issue_tag_link}}', 'issue_id',
			'{{%issue}}', 'id',
			'CASCADE', 'CASCADE'
		);

		$this->addForeignKey('{{%fk_issue_tag_tag}}',
			'{{%issue_tag_link}}', 'tag_id',
			'{{%issue_tag}}', 'id',
			'CASCADE', 'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {

		$this->dropTable('{{%issue_tag_link}}');
		$this->dropTable('{{%issue_tag}}');
	}

}
