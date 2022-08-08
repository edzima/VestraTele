<?php

use console\base\Migration;

/**
 * Class m220805_102302_add_lead_type_column_to_issue_type
 */
class m220805_102302_add_lead_type_column_to_issue_type extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_type}}', 'lead_type_id', $this->integer()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_type}}', 'lead_type_id');
	}

}
