<?php

use console\base\Migration;

/**
 * Class m220113_131816_lead_source-dialer_phone
 */
class m220204_114316_lead_contact_index extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createIndex('index_lead_phone', '{{%lead}}', 'phone');
		$this->createIndex('index_lead_email', '{{%lead}}', 'email');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('index_lead_phone', '{{%lead}}');
		$this->dropIndex('index_lead_email', '{{%lead}}');
	}

}
