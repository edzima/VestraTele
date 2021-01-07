<?php

use console\base\Migration;

/**
 * Class m190916_123401_pays
 */
class m190916_123401_pays extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%issue_pay_calculation}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer()->notNull(),
			'type' => $this->smallInteger()->notNull(),
			'status' => $this->smallInteger()->notNull(),
			'pay_type' => $this->smallInteger()->notNull(),
			'value' => $this->decimal(10, 2)->notNull(),
			'details' => $this->text(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'payment_at' => $this->dateTime(),
			'provider_id' => $this->integer()->notNull(),
			'provider_type' => $this->integer()->notNull(),
		]);

		$this->addForeignKey('fk_issue_pay_calculation_issue', '{{%issue_pay_calculation}}', 'issue_id', '{{%issue}}', 'id');

		$this->createTable('{{%issue_pay}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer()->notNull(),
			'calculation_id' => $this->integer()->notNull(),
			'pay_at' => $this->timestamp()->defaultValue(null),
			'deadline_at' => $this->date()->notNull(),
			'value' => $this->decimal(10, 2)->notNull(),
			'status' => $this->integer()->notNull(),
			'transfer_type' => $this->smallInteger(),
		]);

		$this->addForeignKey('fk_issue_pay_issue', '{{%issue_pay}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_pay_calculation', '{{%issue_pay}}', 'calculation_id', '{{%issue_pay_calculation}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%issue_pay_calculation}}');
		$this->dropTable('{{%issue_pay}}');
	}

}
