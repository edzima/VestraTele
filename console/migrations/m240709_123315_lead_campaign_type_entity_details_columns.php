<?php

use console\base\Migration;

/**
 * Class m240709_123315_lead_campaign_type_entity_details_columns
 */
class m240709_123315_lead_campaign_type_entity_details_columns extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_campaign}}', 'type', $this->string()->null());
		$this->addColumn('{{%lead_campaign}}', 'entity_id', $this->string()->null());
		$this->addColumn('{{%lead_campaign}}', 'details', $this->text()->null());

		$this->dropIndex('{{%lead_campaign_name_owner_unique}}', '{{%lead_campaign}}');
		$this->createIndex('{{%lead_campaign_name_entity_id_unique}}', '{{%lead_campaign}}', ['name', 'entity_id'], true);
		$this->createIndex('lead_cost_campaign_date_unique', '{{%lead_cost}}', ['campaign_id', 'date_at'], true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('lead_cost_campaign_date_unique', '{{%lead_cost}}');
		$this->createIndex('{{%lead_campaign_name_owner_unique}}', '{{%lead_campaign}}', ['name', 'owner_id'], true);
		$this->dropIndex('{{%lead_campaign_name_entity_id_unique}}', '{{%lead_campaign}}');

		$this->dropColumn('{{%lead_campaign}}', 'type');
		$this->dropColumn('{{%lead_campaign}}', 'entity_id');
		$this->dropColumn('{{%lead_campaign}}', 'details');
	}
}
