<?php

use console\base\Migration;

/**
 * Class m200625_111241_many_payments_to_issue
 */
class m200625_111241_many_payments_to_issue extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->dropForeignKey('fk_issue_pay_calculation_issue', '{{%issue_pay_calculation}}');
		$this->dropPrimaryKey('pk_issue_pay_calculation', '{{%issue_pay_calculation}}');
		$this->addColumn('{{%issue_pay_calculation}}', 'id', $this->primaryKey());
		$this->addColumn('{{%issue_pay_calculation}}', 'type', $this->smallInteger()->notNull());
		$this->addColumn('{{%issue_pay_calculation}}', 'payment_at', $this->dateTime());
		$this->addColumn('{{%issue_pay_calculation}}', 'provider_id', $this->integer()->notNull());
		$this->addColumn('{{%issue_pay_calculation}}', 'provider_type', $this->integer()->notNull());

		$this->alterColumn('{{%issue_pay_calculation}}', 'updated_at', $this->timestamp()->defaultValue(null));

		$this->dropColumn('{{%issue_pay_calculation}}', 'pay_type');
		$this->dropColumn('{{%issue_pay_calculation}}', 'status');

		$this->addForeignKey('fk_issue_pay_calculation_issue', '{{%issue_pay_calculation}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');

		$this->addColumn('{{%issue_pay}}', 'calculation_id', $this->integer()->notNull());
		$this->dropColumn('{{%issue_pay}}', 'type');

		$this->execute('SET FOREIGN_KEY_CHECKS=0;');
		$this->dropForeignKey('fk_issue_pay_issue', '{{%issue_pay}}');

		$this->addForeignKey('fk_issue_pay_calculation', '{{%issue_pay}}', 'calculation_id', '{{%issue_pay_calculation}}', 'id', 'CASCADE', 'CASCADE');

		$this->execute('SET FOREIGN_KEY_CHECKS=1;');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addForeignKey('fk_issue_pay_issue', '{{%issue_pay}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');

		$this->dropForeignKey('fk_issue_pay_calculation', '{{%issue_pay}}');
		$this->dropColumn('{{%issue_pay}}', 'calculation_id');

		$this->dropColumn('{{%issue_pay_calculation}}', 'id');
		$this->dropColumn('{{%issue_pay_calculation}}', 'type');
		$this->dropColumn('{{%issue_pay_calculation}}', 'provider_id');
		$this->dropColumn('{{%issue_pay_calculation}}', 'provider_type');
		$this->addColumn('{{%issue_pay_calculation}}', 'pay_type', $this->smallInteger(1)->notNull());

		$this->addColumn('{{%issue_pay}}', 'type', $this->integer()->notNull());

		$this->addPrimaryKey('pk_issue_pay_calculation', '{{%issue_pay_calculation}}', ['issue_id']);
	}

}
