<?php

use console\base\Migration;

/**
 * Class m240328_160815_issue_cost_allow_without_issue
 */
class m240328_160815_issue_cost_allow_without_issue extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue_cost}}', 'issue_id', $this->integer()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('{{%issue_cost}}', 'issue_id', $this->integer()->null());
	}
}
