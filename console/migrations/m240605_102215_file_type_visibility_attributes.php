<?php

use console\base\Migration;

/**
 * Class m240605_102215_file_type_visibility_attributes
 */
class m240605_102215_file_type_visibility_attributes extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%file_type}}', 'visibility_attributes', $this->json()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%file_type}}', 'visibility_attributes');
	}
}
