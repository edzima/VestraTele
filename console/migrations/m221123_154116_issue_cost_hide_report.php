<?php

use console\base\Migration;

/**
 * Class m221123_154116_issue_cost_hide_report
 */
class m221123_154116_issue_cost_hide_report extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_cost}}', 'hide_on_report', $this->boolean());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_cost}}', 'hide_on_report');
	}

}
