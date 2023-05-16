<?php

use console\base\Migration;

/**
 * Class m230426_153522_potential_client_add_phone_column
 */
class m230510_173522_lead_updated_at extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead}}', 'updated_at', $this->timestamp()->defaultExpression('current_timestamp()'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead}}', 'updated_at');
	}

}
