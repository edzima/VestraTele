<?php

use console\base\Migration;

/**
 * Class m231116_143222_issue_shipment_poczta_polska
 */
class m231116_143222_issue_shipment_poczta_polska extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%issue_shipment_poczta_polska}}', [
			'issue_id' => $this->integer()->notNull(),
			'shipment_number' => $this->string()->notNull(),
			'details' => $this->string()->null(),
			'created_at' => $this->dateTime()->notNull(),
			'updated_at' => $this->dateTime()->notNull(),
			'shipment_at' => $this->dateTime()->null(),
			'finished_at' => $this->dateTime()->null(),
			'apiData' => $this->text()->null(),
		]);

		$this->addPrimaryKey(
			'{{%PK_issue_shipment_poczta_polska}}',
			'{{%issue_shipment_poczta_polska}}',
			['issue_id', 'shipment_number']
		);
		$this->addForeignKey(
			'{{%FK_issue_shipment_poczta_polska_issue}}',
			'{{%issue_shipment_poczta_polska}}',
			'issue_id',
			'{{%issue}}',
			'id',
			'CASCADE',
			'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%issue_shipment_poczta_polska}}');
	}
}
