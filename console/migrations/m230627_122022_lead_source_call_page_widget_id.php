<?php

use console\base\Migration;

/**
 * Class m230627_122022_lead_source_call_page_widget_id
 */
class m230627_122022_lead_source_call_page_widget_id extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_source}}', 'call_page_widget_id', $this->integer()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_source}}', 'call_page_widget_id');
	}
}
