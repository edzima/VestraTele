<?php

use console\base\Migration;

/**
 * Class m210716_091157_lead_status_add_report_column
 */
class m210716_091157_lead_status_add_report_column extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_status}}', 'short_report', $this->boolean());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_status}}', 'short_report');
	}

}
