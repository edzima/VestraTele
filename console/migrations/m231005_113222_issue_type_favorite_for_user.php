<?php

use console\base\Migration;

/**
 * Class m231005_113222_issue_type_favorite_for_user
 */
class m231005_113222_issue_type_favorite_for_user extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%user_profile}}', 'favorite_issue_type_id', $this->integer()->null());
		$this->addForeignKey('{{%FK_user_profile_favorite_issue_type}}', '{{%user_profile}}', 'favorite_issue_type_id', '{{%issue_type}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%FK_user_profile_favorite_issue_type}}', '{{%user_profile}}');
		$this->dropColumn('{{%user_profile}}', 'favorite_issue_type_id');
	}
}
