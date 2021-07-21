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
		$this->addColumn('{{%issue_cost}}', 'pay_type', $this->string());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_cost}}', 'pay_type');
	}

}
