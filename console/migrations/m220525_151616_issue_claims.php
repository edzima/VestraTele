<?php

use console\base\Migration;

/**
 * Class m220525_151616_issue_claims
 */
class m220525_151616_issue_claims extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%issue_claim}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer()->notNull(),
			'type' => $this->string(10)->notNull(),
			'entity_responsible_id' => $this->integer()->notNull(),
			'date' => $this->date()->notNull(),
			'trying_value' => $this->decimal(10, 2),
			'obtained_value' => $this->decimal(10, 2),
			'is_percent' => $this->boolean(),
			'details' => $this->string()->null(),
		]);

		$this->addForeignKey('{{%fk_issue_claim_issue}}',
			'{{%issue_claim}}', 'issue_id',
			'{{%issue}}', 'id',
			'CASCADE', 'CASCADE'
		);

		$this->addForeignKey('{{%fk_issue_claim_entity_responsible}}',
			'{{%issue_claim}}', 'entity_responsible_id',
			'{{%issue_entity_responsible}}', 'id',
			'CASCADE', 'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%issue_claim}}');
	}

}
