<?php

use console\base\Migration;

/**
 * Class m230824_113222_entity_responsible_details_and_address
 */
class m230824_113222_entity_responsible_details_and_address extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_entity_responsible}}', 'details', $this->string()->null());
		$this->addColumn('{{%issue_entity_responsible}}', 'address_id', $this->integer()->null());

		$this->addForeignKey(
			'{{%FK_issue_entity_responsible_address}}',
			'{{%issue_entity_responsible}}',
			'address_id',
			'{{%address}}',
			'id',
			'CASCADE',
			'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey(
			'{{%FK_issue_entity_responsible_address}}',
			'{{%issue_entity_responsible}}'
		);
		$this->dropColumn('{{%issue_entity_responsible}}', 'address_id');
		$this->dropColumn('{{%issue_entity_responsible}}', 'details');
	}
}
