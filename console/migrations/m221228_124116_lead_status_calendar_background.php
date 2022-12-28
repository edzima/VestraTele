<?php

use console\base\Migration;

/**
 * Class m221228_124116_lead_status_calendar_background
 */
class m221228_124116_lead_status_calendar_background extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_status}}', '{{%calendar_background}}', $this->string()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_status}}', '{{%calendar_background}}');
	}

}
