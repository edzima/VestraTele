<?php

use console\base\Migration;

/**
 * Class m231005_113222_issue_type_main_type
 */
class m231005_113222_issue_type_main_type extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_type}}', 'is_main', $this->boolean());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_type}}', 'is_main');
	}
}
