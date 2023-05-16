<?php

use console\base\Migration;

/**
 * Class m230515_132022_lead_source_active
 */
class m230515_132022_lead_source_active_and_sms_template_columns extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_source}}', 'is_active', $this->boolean()->notNull()->defaultValue(1));
		$this->addColumn('{{%lead_source}}', 'sms_push_template', $this->text()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_source}}', 'is_active');
		$this->dropColumn('{{%lead_source}}', 'sms_push_template');
	}

}
