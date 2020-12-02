<?php

use console\base\Migration;

/**
 * Class m201120_120929_calculation_owner_and_problem_status
 */
class m201120_120929_calculation_new_feature extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue_pay}}', 'deadline_at', $this->date()->null());

		$this->alterColumn('{{%issue_pay}}', 'status', $this->smallInteger()->null());

		$this->dropColumn('{{%issue_pay}}', 'issue_id');

		$this->dropColumn('{{%issue_pay_calculation}}', 'pay_type');
		$this->dropColumn('{{%issue_pay_calculation}}', 'status');

		$this->addColumn('{{%issue_pay_calculation}}', 'owner_id', $this->integer()->notNull());
		$this->addColumn('{{%issue_pay_calculation}}', 'problem_status', $this->integer());
		$this->addColumn('{{%issue_pay_calculation}}', 'stage_id', $this->smallInteger());

		$this->addForeignKey('{{%fk_issue_pay_calculation_owner}}', '{{%issue_pay_calculation}}', 'owner_id', '{{%user}}', 'id');

		$this->createIndex('{{%issue_pay_calculation_type_index}}', '{{%issue_pay_calculation}}', 'type');

		$this->createTable('{{%settlement_cost}}', [
			'settlement_id' => $this->integer()->notNull(),
			'cost_id' => $this->integer()->notNull(),
		]);

		$this->addForeignKey('{{%fk_settlement_cost_settlement}}', '{{%settlement_cost}}', 'settlement_id', '{{%issue_pay_calculation}}', 'id');
		$this->addForeignKey('{{%fk_settlement_cost_cost}}', '{{%settlement_cost}}', 'cost_id', '{{%issue_cost}}', 'id');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%fk_settlement_cost_settlement}}', '{{%settlement_cost}}');
		$this->dropForeignKey('{{%fk_settlement_cost_cost}}', '{{%settlement_cost}}');
		$this->dropTable('{{%settlement_cost}}');

		$this->addColumn('{{%issue_pay}}', 'issue_id', $this->integer());
		$this->addColumn('{{%issue_pay_calculation}}', 'pay_type', $this->integer());
		$this->addColumn('{{%issue_pay_calculation}}', 'status', $this->integer());

		$this->dropForeignKey('{{%fk_issue_pay_calculation_owner}}', '{{%issue_pay_calculation}}');
		$this->dropIndex('{{%issue_pay_calculation_type_index}}', '{{%issue_pay_calculation}}');

		$this->dropColumn('{{%issue_pay_calculation}}', 'owner_id');
		$this->dropColumn('{{%issue_pay_calculation}}', 'problem_status');
		$this->dropColumn('{{%issue_pay_calculation}}', 'stage_id');
	}

}
