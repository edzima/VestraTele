<?php

use console\base\Migration;

/**
 * Class m210112_212613_add_settlent_costs
 */
class m210112_212613_add_settlent_costs extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%issue_calculation_cost}}', [
			'settlement_id' => $this->integer()->notNull(),
			'cost_id' => $this->integer()->notNull(),
		]);

		$this->addForeignKey('{{%fk_issue_calculation_cost_settlement}}', '{{%issue_calculation_cost}}', 'settlement_id', '{{%issue_pay_calculation}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_issue_calculation_cost_cost}}', '{{%issue_calculation_cost}}', 'cost_id', '{{%issue_cost}}', 'id', 'CASCADE', 'CASCADE');
		$this->addPrimaryKey('{{%pk_issue_calculation_cost}}', '{{%issue_calculation_cost}}', ['settlement_id', 'cost_id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%fk_issue_calculation_cost_settlement}}', '{{%issue_calculation_cost}}');
		$this->dropForeignKey('{{%fk_issue_calculation_cost_cost}}', '{{%issue_calculation_cost}}');
		$this->dropTable('{{%issue_calculation_cost}}');
	}

}
