<?php

use console\base\Migration;

/**
 * Class m240422_140515_leads_costs
 *
 */
class m240422_140515_leads_costs extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%lead_cost}}', [
			'id' => $this->primaryKey(),
			'campaign_id' => $this->integer()->notNull(),
			'value' => $this->decimal(10, 2),
			'date_at' => $this->dateTime()->notNull(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
		]);

		$this->addForeignKey('{{%FK_lead_cost_campaign}}',
			'{{%lead_cost}}',
			'campaign_id',
			'{{%lead_campaign}}',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addColumn('{{%lead_campaign}}', 'is_active', $this->boolean()->defaultValue(1));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%lead_cost}}');
		$this->dropColumn('{{%lead_campaign}}', 'is_active');
	}
}
