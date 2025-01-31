<?php

use console\base\Migration;

/**
 * Class m250108_113611_lead_user_type_index
 */
class m250108_113611_lead_user_type_index extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createIndex(
			'{{%index_lead_user_type}}',
			'{{%lead_user}}',
			['type', 'user_id']
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex(
			'{{%index_lead_user_type}}',
			'{{%lead_user}}',
		);
	}

}
