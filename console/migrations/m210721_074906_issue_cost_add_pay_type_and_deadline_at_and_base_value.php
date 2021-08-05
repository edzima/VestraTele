<?php

use console\base\Migration;

/**
 * Class m210721_074906_issue_cost_pay_type
 */
class m210721_074906_issue_cost_add_pay_type_and_deadline_at_and_base_value extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue_cost}}', 'settled_at', $this->date());
		$this->addColumn('{{%issue_cost}}', 'confirmed_at', $this->date());
		$this->addColumn('{{%issue_cost}}', 'deadline_at', $this->date());
		$this->addColumn('{{%issue_cost}}', 'base_value', $this->decimal(10, 2));
		$this->addColumn('{{%issue_cost}}', 'transfer_type', $this->string());
		$this->createIndex('{{%issue_cost_index_transfer_type}}', '{{%issue_cost}}', 'transfer_type');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('{{%issue_cost}}', 'settled_at', $this->dateTime());
		$this->dropColumn('{{%issue_cost}}', 'confirmed_at');

		$this->dropColumn('{{%issue_cost}}', 'deadline_at');
		$this->dropColumn('{{%issue_cost}}', 'base_value');

		$this->dropIndex('{{%issue_cost_index_transfer_type}}', '{{%issue_cost}}');
		$this->dropColumn('{{%issue_cost}}', 'transfer_type');
	}

}
