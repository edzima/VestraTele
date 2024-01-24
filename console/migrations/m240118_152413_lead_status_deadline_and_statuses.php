<?php

use console\base\Migration;

/**
 * Class m240118_152413_lead_status_deadline_and_statuses
 */
class m240118_152413_lead_status_deadline_and_statuses extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_status}}', 'days_deadline', $this->smallInteger());
		$this->addColumn('{{%lead_status}}', 'statuses', $this->string()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_status}}', 'days_deadline');
		$this->dropColumn('{{%lead_status}}', 'statuses');
	}
}
