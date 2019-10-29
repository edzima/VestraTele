<?php

use console\base\Migration;
use yii\db\Expression;

/**
 * Class m190916_123401_pays
 */
class m190916_123401_pays extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->addColumn('{{%issue}}', 'pay_city_id', $this->integer());
		$this->addForeignKey('fk_issue_pay_city', '{{%issue}}', 'pay_city_id', '{{%miasta}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%issue_pay_city}}', [
			'city_id' => $this->integer()->notNull(),
			'phone' => $this->string(15),
			'bank_transfer_at' => $this->dateTime(),
			'direct_at' => $this->dateTime(),
		]);

		$this->addPrimaryKey('pk_issue_pay_city', '{{%issue_pay_city}}', 'city_id');
		$this->addForeignKey('fk_issue_pay_city_city', '{{%issue_pay_city}}', 'city_id', '{{%miasta}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%issue_entity_responsible_details}}', [
			'city_id' => $this->integer()->notNull(),
			'entity_id' => $this->integer()->notNull(),
			'phone' => $this->string(15),
			'bank_transfer_at' => $this->dateTime(),
			'direct_at' => $this->dateTime(),
		]);

		$this->addPrimaryKey('pk_issue_entity_responsible_details', '{{%issue_entity_responsible_details}}', ['city_id', 'entity_id']);
		$this->addForeignKey('fk_issue_entity_responsible_details_city', '{{%issue_entity_responsible_details}}', 'city_id', '{{%miasta}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_entity_responsible_details_entity', '{{%issue_entity_responsible_details}}', 'entity_id', '{{%issue_entity_responsible}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%issue_pay_calculation}}', [
			'issue_id' => $this->integer()->notNull(),
			'status' => $this->smallInteger(1)->notNull(),
			'value' => $this->decimal(10, 2)->notNull(),
			'pay_type' => $this->smallInteger(1)->notNull(),
			'details' => $this->text(),
			'created_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
			'updated_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
		]);

		$this->addPrimaryKey('pk_issue_pay_calculation', '{{%issue_pay_calculation}}', ['issue_id']);
		$this->addForeignKey('fk_issue_pay_calculation_issue', '{{%issue_pay_calculation}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');

		$this->truncateTable('{{%issue_pay}}');
		$this->renameColumn('{{%issue_pay}}', 'date', 'pay_at');
		$this->alterColumn('{{%issue_pay}}', 'pay_at', $this->timestamp()->defaultValue(null));
		$this->addColumn('{{%issue_pay}}', 'deadline_at', $this->dateTime()->notNull());
		$this->addColumn('{{%issue_pay}}', 'transfer_type', $this->smallInteger()->notNull());

		$this->addColumn('{{%issue_note}}', 'type', $this->smallInteger());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {

		$this->dropColumn('{{%issue_note}}', 'type');
		$this->dropColumn('{{%issue_pay}}', 'transfer_type');
		$this->dropColumn('{{%issue_pay}}', 'deadline_at');
		$this->renameColumn('{{%issue_pay}}', 'pay_at', 'date');
		$this->dropTable('{{%issue_pay_calculation}}');
		$this->dropTable('{{%issue_entity_responsible_details}}');
		$this->dropTable('{{%issue_pay_city}}');

		$this->dropForeignKey('fk_issue_pay_city', '{{%issue}}');
		$this->dropColumn('{{%issue}}', 'pay_city_id');
	}

}
