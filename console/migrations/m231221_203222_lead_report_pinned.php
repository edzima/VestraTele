<?php

use console\base\Migration;

/**
 * Class m231221_203222_lead_report_pinned
 */
class m231221_203222_lead_report_pinned extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_report}}', 'is_pinned', $this->boolean());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_report}}', 'is_pinned');
	}
}
