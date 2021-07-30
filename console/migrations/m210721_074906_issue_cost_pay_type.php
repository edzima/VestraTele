<?php

use console\base\Migration;

/**
 * Class m210721_074906_issue_cost_pay_type
 */
class m210721_074906_issue_cost_pay_type extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_cost}}', 'transfer_type', $this->string());
		$this->createIndex('{{%issue_cost_index_transfer_type}}', '{{%issue_cost}}', 'transfer_type');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('{{%issue_cost_index_transfer_type}}', '{{%issue_cost}}');
		$this->dropColumn('{{%issue_cost}}', 'transfer_type');
	}

}
