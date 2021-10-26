<?php

use console\base\Migration;

/**
 * Class m211025_093702_settlement_null_vat
 */
class m211025_093702_settlement_null_vat extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue_pay}}', 'vat', $this->decimal(5, 2)->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('{{%issue_pay}}', 'vat', $this->decimal(5, 2)->notNull());
	}

}
