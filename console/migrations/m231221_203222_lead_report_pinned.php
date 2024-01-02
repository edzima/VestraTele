<?php

use console\base\Migration;

/**
 * Class m231116_143222_issue_shipment_poczta_polska
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
