<?php

use console\base\Migration;

/**
 * Class m241113_110710_settlement_type_percentage
 */
class m241113_110710_settlement_type_percentage extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%settlement_type}}', 'is_percentage', $this->boolean()->defaultExpression(0));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%settlement_type}}', 'is_percentage');
	}

}
