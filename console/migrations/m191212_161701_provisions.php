<?php

use console\base\Migration;

/**
 * Class m191212_161701_provisions
 */
class m191212_161701_provisions extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->addColumn('{{%issue_type}}', 'vat', $this->decimal(5, 2)->notNull());
		$this->addColumn('{{%issue_pay}}', 'vat', $this->decimal(5, 2)->notNull());

		$this->createTable('{{%provision_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull(),
			'value' => $this->decimal(10, 2)->notNull(),
			'date_from' => $this->timestamp()->null(),
			'date_to' => $this->timestamp()->null(),
			'only_with_tele' => $this->boolean(),
			'is_default' => $this->boolean(),
			'data' => $this->string(),
			'is_percentage' => $this->boolean()->defaultValue(1),
		]);

		$this->createTable('{{%provision_user}}', [
			'from_user_id' => $this->integer()->notNull(),
			'to_user_id' => $this->integer()->notNull(),
			'type_id' => $this->integer()->notNull(),
			'value' => $this->decimal(10, 2)->notNull(),
		]);

		$this->addPrimaryKey('{{%pk_provision_user}}', '{{%provision_user}}', ['from_user_id', 'to_user_id', 'type_id']);
		$this->addForeignKey('{{%fk_provision_user_from_user}}', '{{%provision_user}}', 'from_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_provision_user_to_user}}', '{{%provision_user}}', 'to_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_provision_user_type}}', '{{%provision_user}}', 'type_id', '{{%provision_type}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%provision}}', [
			'id' => $this->primaryKey(),
			'pay_id' => $this->integer()->notNull(),
			'to_user_id' => $this->integer()->notNull(),
			'from_user_id' => $this->integer(),
			'value' => $this->decimal(10, 2)->notNull(),
			'type_id' => $this->integer()->notNull(),
			'hide_on_report' => $this->boolean(),
		]);

		$this->addForeignKey('{{%fk_provision_pay}}', '{{%provision}}', 'pay_id', '{{%issue_pay}}', 'id', 'CASCADE', 'CASCADE');

		$this->addForeignKey('{{%fk_provision_from_user}}', '{{%provision}}', 'from_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_provision_to_user}}', '{{%provision}}', 'to_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

		$this->addForeignKey('{{%fk_provision_type}}', '{{%provision}}', 'type_id', '{{%provision_type}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {

		$this->dropColumn('{{%issue_type}}', 'vat');
		$this->dropColumn('{{%issue_pay}}', 'vat');

		$this->dropForeignKey('{{%fk_provision_type}}', '{{%provision}}');
		$this->dropForeignKey('{{%fk_provision_from_user}}', '{{%provision}}');
		$this->dropForeignKey('{{%fk_provision_to_user}}', '{{%provision}}');
		$this->dropForeignKey('{{%fk_provision_pay}}', '{{%provision}}');

		$this->dropForeignKey('{{%fk_provision_user_from_user}}', '{{%provision_user}}');
		$this->dropForeignKey('{{%fk_provision_user_to_user}}', '{{%provision_user}}');

		$this->dropForeignKey('{{%fk_provision_user_type}}', '{{%provision_user}}');

		$this->dropTable('{{%provision}}');
		$this->dropTable('{{%provision_type}}');
		$this->dropTable('{{%provision_user}}');
	}

}
