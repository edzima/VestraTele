<?php

use console\base\Migration;

/**
 * Class m231005_113222_issue__type_ead_source_id
 */
class m231005_113222_issue_type_lead_source_id extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_type}}', 'lead_source_id', $this->integer()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_type}}', 'lead_source_id');
	}
}
