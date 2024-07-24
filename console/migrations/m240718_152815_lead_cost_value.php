<?php

use console\base\Migration;

/**
 * Class m240709_123315_lead_campaign_type_entity_details_columns
 */
class m240718_152815_lead_cost_value extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead}}', 'cost_value', $this->decimal(10, 2)->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead}}', 'cost_value');
	}

}
