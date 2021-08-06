<?php

use console\base\Migration;

/**
 * Class m210806_090112_lead_status_calendar_data
 */
class m210806_090112_lead_status_calendar_data extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_status}}', 'calendar', $this->json());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_status}}', 'calendar');
	}

}
