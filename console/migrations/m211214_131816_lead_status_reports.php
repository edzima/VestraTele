<?php

use console\base\Migration;

/**
 * Class m211214_131816_lead_status_reports
 */
class m211214_131816_lead_status_reports extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_status}}', 'show_report_in_lead_index', $this->boolean()->defaultValue(1));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_status}}', 'show_report_in_lead_index');
	}

}
