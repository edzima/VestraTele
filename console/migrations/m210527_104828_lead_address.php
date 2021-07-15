<?php

use console\base\Migration;

/**
 * Class m210527_104828_lead_address
 */
class m210527_104828_lead_address extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp(): void {
		$this->createTable('{{%lead_address}}', [
			'lead_id' => $this->integer()->notNull(),
			'address_id' => $this->integer()->notNull(),
			'type' => $this->string()->notNull(),
		]);

		$this->addPrimaryKey('{{%PK_lead_address}}', '{{%lead_address}}', ['lead_id', 'type']);
		$this->addForeignKey('{{%fk_lead_address_lead}}', '{{%lead_address}}', 'lead_id', '{{%lead}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_lead_address_address}}', '{{%lead_address}}', 'address_id', '{{%address}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): void {
		$this->dropTable('{{%lead_address}}');
	}

}
