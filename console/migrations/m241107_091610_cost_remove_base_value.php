<?php

use console\base\Migration;

/**
 * Class m241107_091610_cost_remove_base_value
 */
class m241107_091610_cost_remove_base_value extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropColumn('{{%issue_cost}}', 'base_value');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addColumn('{{%issue_cost}}', 'base_value', $this->decimal(10, 2)->null());
	}

}
