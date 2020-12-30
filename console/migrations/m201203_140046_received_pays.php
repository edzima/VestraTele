<?php

use console\base\Migration;

/**
 * Class m201203_140046_received_pays
 */
class m201203_140046_received_pays extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%pay_received}}', [
			'pay_id' => $this->primaryKey(),
			'user_id' => $this->integer()->notNull(),
			'date_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'transfer_at' => $this->timestamp()->null()->defaultValue(null),
		]);

		$this->addForeignKey('{{%fk_pay_received_pay}}', '{{%pay_received}}', 'pay_id', '{{%issue_pay}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_pay_received_user}}', '{{%pay_received}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%fk_pay_received_pay}}', '{{%pay_received}}');
		$this->dropForeignKey('{{%fk_pay_received_user}}', '{{%pay_received}}');
		$this->dropTable('{{%pay_received}}');
	}

}
