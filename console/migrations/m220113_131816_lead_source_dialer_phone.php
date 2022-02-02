<?php

use console\base\Migration;

/**
 * Class m220113_131816_lead_source-dialer_phone
 */
class m220113_131816_lead_source_dialer_phone extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_source}}', 'dialer_phone', $this->string(20));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_source}}', 'dialer_phone');
	}

}
