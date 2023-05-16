<?php

use console\base\Migration;

/**
 * Class m230510_173522_lead_report_deleted_at
 */
class m230510_173522_lead_report_deleted_at extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_report}}', 'deleted_at', $this->timestamp()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_report}}', 'deleted_at');
	}

}
