<?php

use console\base\Migration;

/**
 * Class m240826_162401_lead_status_deal_stage
 */
class m240826_162401_lead_status_deal_stage extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_status}}', 'deal_stage', $this->smallInteger()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_status}}', 'deal_stage');
	}

}
