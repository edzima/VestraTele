<?php

use console\base\Migration;

/**
 * Class m210225_154648_issue_cost_add_user
 */
class m210225_154648_issue_cost_add_user_and_settled_at extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_cost}}', 'settled_at', $this->dateTime()->null());

		$this->addColumn('{{%issue_cost}}', 'user_id', $this->integer());
		$this->addForeignKey('{{%fk_issue_cost_user}}', '{{%issue_cost}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_cost}}', 'settled_at');
		$this->dropForeignKey('{{%fk_issue_cost_user}}', '{{%issue_cost}}');
		$this->dropColumn('{{%issue_cost}}', 'user_id');
	}

}
