<?php

use console\base\Migration;

/**
 * Class m231116_143222_issue_shipment_poczta_polska
 */
class m231205_103222_issue_signature_act_limit_length extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue}}', 'signature_act', $this->string(255));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
	}
}
